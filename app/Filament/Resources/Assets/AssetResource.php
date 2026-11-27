<?php

namespace App\Filament\Resources\Assets;

use App\Filament\AssetDocxTransfer;
use App\Filament\Resources\Assets\Pages\ManageAssets;
use App\Models\Asset;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Select::make('brand_id')
                    ->relationship('brand', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('serial_number')
                    ->required(),
                TextInput::make('asset_code')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('expiry_date'),
                Select::make('classroom_id')
                    ->relationship('classroom', 'room_number')
                    ->required(),
                Select::make('building_id')
                    ->relationship('building', 'name')
                    ->required(),
                Select::make('status')
                    ->required()
                    ->default('available')
                    ->options([
                        'available' => 'Available',
                        'in_use' => 'In Use',
                        'maintenance' => 'Maintenance',
                        'for_disposal' => 'For Disposal',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('asset_code')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('brand.name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('serial_number')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('terminal_number')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('classroom.room_number')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('building.name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'available' => 'success',
                            'in_use' => 'warning',
                            'maintenance' => 'danger',
                            'for_disposal' => 'secondary',
                            default => 'primary',
                        };
                    })
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('expiry_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('brand')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('classroom')
                    ->relationship('classroom', 'room_number')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('building')
                    ->relationship('building', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'in_use' => 'In Use',
                        'maintenance' => 'Maintenance',
                        'for_disposal' => 'For Disposal',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                Action::make('exportDocx')
                    ->label('Export DOCX')
                    ->color('info')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (HasTable $livewire, AssetDocxTransfer $transfer) => $transfer->download(
                        $livewire->getTableQueryForExport(),
                    )),
                Action::make('importDocx')
                    ->label('Import DOCX')
                    ->color('info')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->schema([
                        FileUpload::make('file')
                            ->label('DOCX file')
                            ->disk('local')
                            ->directory('asset-imports')
                            ->visibility('private')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/zip',
                                'application/x-zip-compressed',
                            ])
                            ->maxSize(10240)
                            ->required(),
                    ])
                    ->action(function (array $data, AssetDocxTransfer $transfer): void {
                        $filePath = $data['file'] ?? null;

                        if (! is_string($filePath) || $filePath === '') {
                            throw ValidationException::withMessages([
                                'file' => 'Select a DOCX file to import.',
                            ]);
                        }

                        try {
                            $importedRows = $transfer->import(Storage::disk('local')->path($filePath));
                        } finally {
                            if (Storage::disk('local')->exists($filePath) && ! Storage::disk('local')->delete($filePath)) {
                                throw new RuntimeException('Unable to remove the uploaded DOCX file.');
                            }
                        }

                        Notification::make()
                            ->title('Asset import complete')
                            ->body("{$importedRows} ".str('asset')->plural($importedRows).' imported.')
                            ->success()
                            ->send();
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAssets::route('/'),
        ];
    }
}

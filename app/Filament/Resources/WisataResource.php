<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WisataResource\Pages;
use App\Filament\Resources\WisataResource\RelationManagers;
use App\Models\Category;
use App\Models\Wisata;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class WisataResource extends Resource
{
    protected static ?string $model = Wisata::class;

    protected static ?string $navigationLabel = 'Kelola Wisata';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns([
                'xl' => 5,
            ])
            ->schema([
                Forms\Components\Hidden::make('slug'),

                Forms\Components\TextInput::make('nama')
                    ->maxLength(150)
                    ->columnSpan([
                        'xl' => 3,
                    ])
                    ->live(debounce: '500ms')
                    ->afterStateUpdated(function($set, $state, $context) {
                        $set('slug', Str::slug($state));
                    }),
                Forms\Components\TextInput::make('harga')
                    ->numeric()
                    ->columnSpan([
                        'xl' => 1,
                    ])
                    ->prefix('Rp'),
                Forms\Components\Select::make('category_id')
                        ->label('Kategori')
                        ->options(Category::all()->pluck('name', 'id'))
                        ->columnSpan([
                            'xl' => 1,
                        ]),
                Forms\Components\TextInput::make('lokasi')
                    ->maxLength(200)
                    ->columnSpan([
                        'xl' => 5,
                    ]),
                Forms\Components\RichEditor::make('deskripsi')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('foto')
                    ->image()
                    ->columnSpan([
                        'xl' => 2,
                    ]),
                Map::make('location')
                    ->columnSpan([
                        'xl' => 3,
                    ])
                    ->label('Lokasi Wisata')
                    ->columnSpanFull()
                    ->default([
                        'lat' => 40.4168,
                        'lng' => -3.7038
                    ])
                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);
                    })
                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                        $set('location', ['lat' => $record->latitude ?? '-6.736070', 'lng' => $record->longitude ?? '108.467888']);
                    })
                    ->extraStyles([
                        'min-height: 300px',
                    ])
                    ->liveLocation()
                    ->showMarker()
                    ->markerColor("#22c55eff")
                    ->showFullscreenControl()
                    ->showZoomControl()
                    ->draggable()
                    ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                    ->zoom(10)
                    ->detectRetina()
                    ->showMyLocationButton()
                    ->extraTileControl([])
                    ->extraControl([
                        'zoomDelta'           => 1,
                        'zoomSnap'            => 2,
                ]),
                Forms\Components\TextInput::make('latitude')
                    ->maxLength(100)
                    ->columnStart([
                        'xl' => 3,
                    ]),
                Forms\Components\TextInput::make('longitude')
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->hidden(),
                Tables\Columns\TextColumn::make('harga')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('foto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->searchable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWisatas::route('/'),
            'create' => Pages\CreateWisata::route('/create'),
            'edit' => Pages\EditWisata::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\MeterTariff;

use App\Models\MeterTariff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class MeterTariffResource extends Resource
{
    protected static ?string $model = MeterTariff::class;

    protected static ?string $slug = 'meter-tariffs';

    protected static ?string $navigationGroup = 'Meters';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('meter_id')
                    ->relationship('meter', 'code')
                    ->label('Meter')
                    ->placeholder('Select Meter')
                    ->required(),
                Forms\Components\Select::make('tariff_id')
                    ->relationship('tariff', 'name')
                    ->label('Tariff')
                    ->placeholder('Select Tariff')
                    ->required(),
                Forms\Components\DateTimePicker::make('effective_from')
                    ->label('Effective From')
                    ->placeholder('Pick Effective From')
                    ->required(),
                Forms\Components\DateTimePicker::make('effective_to')
                    ->label('Effective To')
                    ->placeholder('Pick Effective To'),

            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('meter.code')
                    ->label('Meter')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tariff.name')
                    ->label('Tariff')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('effective_from')
                    ->label('Effective From')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('effective_to')
                    ->label('Effective To')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Feb 17, '.now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from '.Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until '.Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                Tables\Filters\Filter::make('updated_at')
                    ->form([
                        Forms\Components\DatePicker::make('updated_from')
                            ->placeholder(fn ($state): string => 'Feb 17, '.now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('updated_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['updated_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['updated_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['updated_from'] ?? null) {
                            $indicators['updated_from'] = 'Order from '.Carbon::parse($data['updated_from'])->toFormattedDateString();
                        }
                        if ($data['updated_until'] ?? null) {
                            $indicators['updated_until'] = 'Order until '.Carbon::parse($data['updated_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('meter.code')
                    ->label('Meter'),
                Infolists\Components\TextEntry::make('tariff.name')
                    ->label('Tariff'),
                Infolists\Components\TextEntry::make('effective_from')
                    ->label('Effective From')
                    ->dateTime(),
                Infolists\Components\TextEntry::make('effective_to')
                    ->label('Effective To')
                    ->dateTime(),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
                Infolists\Components\TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime(),
            ])
            ->columns(1)
            ->inlineLabel();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeterTariff::route('/'),
            // 'create' => Pages\CreateMeterTariff::route('/create'),
            // 'edit' => Pages\EditMeterTariff::route('/{record}/edit'),
            // 'view' => Pages\ViewMeterTariff::route('/{record}'),
        ];
    }
}

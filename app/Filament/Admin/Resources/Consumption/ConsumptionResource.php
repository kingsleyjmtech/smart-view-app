<?php

namespace App\Filament\Admin\Resources\Consumption;

use App\Models\Consumption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ConsumptionResource extends Resource
{
    protected static ?string $model = Consumption::class;
    
    protected static ?string $slug = 'consumptions';
    
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
                Forms\Components\Select::make('aggregation_period')
                    ->label('Aggregation Period')
                    ->placeholder('Select Aggregation Period')
                    ->options(Consumption::AGGREGATION_PERIOD_SELECT),
                Forms\Components\TextInput::make('value')
                    ->label('Value')
                    ->placeholder('Enter Value')
                    ->required(),
                Forms\Components\DateTimePicker::make('date')
                    ->label('Date')
                    ->placeholder('Pick Date')
                    ->required(),
                
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
                Tables\Columns\TextColumn::make('aggregation_period')
                    ->label('Aggregation Period')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
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
                            ->placeholder(fn ($state): string => 'Feb 17, ' . now()->subYear()->format('Y')),
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
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                Tables\Filters\Filter::make('updated_at')
                    ->form([
                        Forms\Components\DatePicker::make('updated_from')
                            ->placeholder(fn ($state): string => 'Feb 17, ' . now()->subYear()->format('Y')),
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
                            $indicators['updated_from'] = 'Order from ' . Carbon::parse($data['updated_from'])->toFormattedDateString();
                        }
                        if ($data['updated_until'] ?? null) {
                            $indicators['updated_until'] = 'Order until ' . Carbon::parse($data['updated_until'])->toFormattedDateString();
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
                Infolists\Components\TextEntry::make('aggregation_period')
                    ->label('Aggregation Period'),
                Infolists\Components\TextEntry::make('value')
                    ->label('Value'),
                Infolists\Components\TextEntry::make('date')
                   ->label('Date')
                   ->dateTime(),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
                Infolists\Components\TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
            ])
            ->columns(1)
            ->inlineLabel();
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsumption::route('/'),
            // 'create' => Pages\CreateConsumption::route('/create'),
            // 'edit' => Pages\EditConsumption::route('/{record}/edit'),
            // 'view' => Pages\ViewConsumption::route('/{record}'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\Meter;

use App\Models\Meter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class MeterResource extends Resource
{
    protected static ?string $model = Meter::class;

    protected static ?string $slug = 'meters';

    protected static ?string $navigationGroup = 'Meters';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'uuid')
                    ->label('Tenant')
                    ->placeholder('Select Tenant')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'email')
                    ->label('User')
                    ->placeholder('Select User'),
                Forms\Components\Select::make('utility_type_id')
                    ->relationship('utilityType', 'name')
                    ->label('Utility Type')
                    ->placeholder('Select Utility Type')
                    ->required(),
                Forms\Components\TextInput::make('code')
                    ->label('Code')
                    ->placeholder('Enter Code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('location')
                    ->label('Location')
                    ->placeholder('Enter Location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('installation_date')
                    ->label('Installation Date')
                    ->placeholder('Pick Installation Date'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->placeholder('Select Status')
                    ->options(Meter::STATUS_SELECT)
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
                Tables\Columns\TextColumn::make('tenant.uuid')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('utilityType.name')
                    ->label('Utility Type')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('installation_date')
                    ->label('Installation Date')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
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
                Infolists\Components\TextEntry::make('tenant.uuid')
                    ->label('Tenant'),
                Infolists\Components\TextEntry::make('user.email')
                    ->label('User'),
                Infolists\Components\TextEntry::make('utilityType.name')
                    ->label('Utility Type'),
                Infolists\Components\TextEntry::make('code')
                    ->label('Code'),
                Infolists\Components\TextEntry::make('location')
                    ->label('Location'),
                Infolists\Components\TextEntry::make('installation_date')
                    ->label('Installation Date')
                    ->date(),
                Infolists\Components\TextEntry::make('status')
                    ->label('Status'),
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
            'index' => Pages\ListMeter::route('/'),
            // 'create' => Pages\CreateMeter::route('/create'),
            // 'edit' => Pages\EditMeter::route('/{record}/edit'),
            // 'view' => Pages\ViewMeter::route('/{record}'),
        ];
    }
}

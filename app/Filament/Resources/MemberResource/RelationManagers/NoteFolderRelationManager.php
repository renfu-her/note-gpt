<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class NoteFolderRelationManager extends RelationManager
{
    protected static string $relationship = 'noteFolders';
    protected static ?string $title = '資料夾';
    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('編號')->sortable(),
                Tables\Columns\TextColumn::make('parent.name')->label('父層資料夾')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('名稱')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('sort_order')->label('排序')->sortable(),
                Tables\Columns\BooleanColumn::make('is_active')->label('啟用'),
                Tables\Columns\TextColumn::make('created_at')->label('建立時間')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('新增資料夾'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('編輯'),
                Tables\Actions\DeleteAction::make()->label('刪除'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('批次刪除'),
                ]),
            ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'name', fn ($query) => $query->whereNull('parent_id'))
                    ->label('父層資料夾')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->label('名稱')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('描述')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('啟用')
                    ->default(true),
            ]);
    }
} 
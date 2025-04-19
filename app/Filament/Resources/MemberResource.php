<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = '會員管理';
    protected static ?string $modelLabel = '會員';
    protected static ?string $pluralModelLabel = '會員';
    protected static ?string $navigationGroup = '系統管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('姓名')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('電話')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('電子郵件')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('密碼')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => bcrypt($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('birthday')
                    ->label('生日')
                    ->required(),
                Forms\Components\Textarea::make('note')
                    ->label('備註')
                    ->rows(3),
                Forms\Components\Toggle::make('is_active')
                    ->label('啟用')
                    ->inline(false)
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('編號')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('姓名')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('電話')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('電子郵件')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('birthday')->label('生日')->date(),
                Tables\Columns\TextColumn::make('note')->label('備註')->limit(50),
                Tables\Columns\BooleanColumn::make('is_active')->label('啟用'),
                Tables\Columns\TextColumn::make('created_at')->label('建立時間')->dateTime(),
                Tables\Columns\BadgeColumn::make('notes_count')
                    ->counts('notes')
                    ->label('筆記數'),
            ])
            ->filters([
                // 依需求新增過濾器
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('編輯'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('批次刪除'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\NoteRelationManager::class,
            RelationManagers\NoteFolderRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFavoriteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    // お気に入りを追加（更新）
    public function up()
    {
        //  user_favoriteテーブルを作成
        Schema::create('user_favorite', function (Blueprint $table) {
            //  以下のカラムを作成
            $table->increments('id');
            //  unsigned()は負の数は許可しない。index()は検索速度を高める。
            $table->integer('user_id')->unsigned()->index();
            $table->integer('micropost_id')->unsigned()->index();
            $table->timestamps();
            
            // $table->foreign(外部キーを設定するカラム名)->references(制約先のID名)->on(外部キー制約先のテーブル名);
            // onDelete は参照先のデータが削除されたときにこのテーブルの行をどのように扱うかを指定します。
            // onDelete('cascade') で、ユーザー（マイクロポスト）テーブルのユーザー（マイクロポスト）データが削除されたら、それにひもづくお気に入りテーブルのお気に入りレコードも削除される
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('micropost_id')->references('id')->on('microposts')->onDelete('cascade');

            // user_idとmicropost_idの組み合わせの重複を許さない
            $table->unique(['user_id', 'micropost_id']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //  お気に入りを削除
        Schema::drop('user_favorite');
    }
}

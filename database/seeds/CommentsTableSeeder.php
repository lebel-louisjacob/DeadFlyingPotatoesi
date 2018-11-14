<?php

use Illuminate\Database\Seeder;
use App\Comment;
use Carbon\Carbon;

class CommentsTableSeeder extends Seeder
{

    public function run()
    {
        Comment::create([
            'station_id' => 1,
            'user_id' =>3,
            'text' => 'Bacon ipsum dolor amet jowl brisket flank chuck, 
                       t-bone landjaeger pastrami porchetta bresaola. Shankle ball
                       tip pork belly ribeye boudin doner. Bacon ipsum dolor amet
                       jowl brisket flank chuck, t-bone landjaeger pastrami porchetta 
                       bresaola. Shankle ball tip pork belly ribeye boudin doner.
                       Bacon ipsum dolor amet jowl brisket flank chuck, t-bone
                       landjaeger pastrami porchetta bresaola.
                       Bacon ipsum dolor amet jowl brisket flank chuck, 
                       Bacon ipsum dolor amet jowl brisket flank chuck, 
                       t-bone landjaeger pastrami porchetta bresaola. 
                       Shankle ball tip pork belly ribeye boudin doner.
                       Shankle ball tip pork belly ribeye boudin doner.',
            'created_at' => Carbon::now()->subHours(10)
        ]);

        Comment::create([
            'station_id' => 1,
            'user_id' =>2,
            'text' => 'Test de commentaire pour l\'utilisateur numéro 2',
            'created_at' => Carbon::now(),
        ]);

        Comment::create([
            'station_id' => 2,
            'user_id' =>2,
            'text' => 'Test de commentaire pour la station numero 2',
            'created_at' => Carbon::now()
        ]);

    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;

class CommentAPITest extends TestCase
{
    use DatabaseTransactions;
    const MAX_TEXT_LENGTH = 1024;
    const MIN_TEXT_LENGTH = 1;

    public function testTryGetCommentsFromStationThatDoesntExist()
    {
        $response = $this->get('/api/stations/100/comments');
        $response->assertStatus(404);
    }

    public function testGetCommentsFromAnExistingStation()
    {
        $response = $this->get('/api/stations/1/comments');
        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function testPostNewCommentWithValidTextWhenAuthenficated()
    {
        Passport::actingAs(\App\User::find(1));
        $textToPost = $this->generateRandomText(10);
        $response = $this->post('api/stations/1/comments',
            ['text'=> $textToPost],
            ['Accept'=> 'application/json']);
        $response->assertJsonFragment(['text'=>$textToPost]);
        $response->assertJsonFragment(['user_id'=>1]);
        $response->assertJsonFragment(['station_id'=>1]);
        $response->assertStatus(201);
    }

    public function testPostNewCommentWithNoTextInItWhenAuthenticated()
    {
        Passport::actingAs(\App\User::find(1));
        $response = $this->post('api/stations/1/comments',
            [],
            ['Accept'=> 'application/json']);
        $response->assertJsonFragment(['text'=>['The text field is required.']]);
        $response->assertStatus(422);
    }

    public function testPostNewCommentWithAnInvalidTextInItWhenAuthenticated()
    {
        Passport::actingAs(\App\User::find(1));
        $response = $this->post('api/stations/1/comments',
            ['text'=> 2],
            ['Accept'=> 'application/json']);
        $response->assertJsonFragment(['text'=>['The text must be a string.']]);
        $response->assertStatus(422);
    }

    public function testPostNewCommentWithMaximumTextLengthWhenAuthenticated()
    {
        Passport::actingAs(\App\User::find(1));
        $textToPost = $this->generateRandomText(self::MAX_TEXT_LENGTH);
        $response = $this->post('api/stations/1/comments',
            ['text'=> $textToPost ],
            ['Accept'=> 'application/json']);
        $response->assertJsonFragment(['user_id'=>1]);
        $response->assertJsonFragment(['station_id'=>1]);
        $response->assertJsonFragment(['text'=>$textToPost]);
        $response->assertStatus(201);
    }

    public function testPostNewCommentWithMinimumTextLengthWhenAuthenticated()
    {
        Passport::actingAs(\App\User::find(2));
        $textToPost = $this->generateRandomText(self::MIN_TEXT_LENGTH);
        $response = $this->post('api/stations/1/comments',
            ['text'=> $textToPost ],
            ['Accept'=> 'application/json']);
        $response->assertJsonFragment(['user_id'=>2]);
        $response->assertJsonFragment(['station_id'=>1]);
        $response->assertJsonFragment(['text'=>$textToPost]);
        $response->assertStatus(201);
    }

    public function testTryPostNewCommentInANonExistingStationWhenAuthenticated()
    {
        Passport::actingAs(\App\User::find(1));
        $response = $this->post('api/stations/100/comments',
            ['text'=> $this->generateRandomText(self::MAX_TEXT_LENGTH) ],
            ['Accept'=> 'application/json']);
        $response->assertStatus(404);
    }

    public function testPostNewCommentWithTooLongTextWhenAuthenticated()
    {
        Passport::actingAs(\App\User::find(1));
        $response = $this->post('api/stations/1/comments',
            ['text'=> $this->generateRandomText(self::MAX_TEXT_LENGTH + 1)],
            ['Accept'=> 'application/json']);
        $response->assertJsonFragment(['text'=>["The text may not be greater than 1024 characters."]]);
        $response->assertStatus(422);
    }

    public function testPostNewCommentWithAValidTextWhenNotAuthenticated()
    {
        $response = $this->post('api/stations/1/comments',
            ['text'=> $this->generateRandomText(self::MAX_TEXT_LENGTH)],
            ['Accept'=> 'application/json']);
        $response->assertStatus(401);
    }

    private function generateRandomText($length){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $string;
    }
}

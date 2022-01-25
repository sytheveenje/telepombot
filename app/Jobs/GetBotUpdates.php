<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\Participant;
use App\Models\Update;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Telegram\Bot\Api;

class GetBotUpdates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const ENTER = "ikdoemee";
    const QUIT = "stophouop";
    const CHECK = "check";
    const CHECKS = "checks";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $updates = $telegram->getUpdates();

        foreach ($updates as $update) {
            $updateNeedsProcessing = Update::where('update_id', $update['update_id'])->first();

            if (!$updateNeedsProcessing) {

                Update::create([
                    'update_id' => $update['update_id'],
                    'status' => $update
                ]);

                if ($update && Arr::exists($update, 'message') && Arr::exists($update['message'], 'entities') && $update['message']['entities'][0]['type'] === 'bot_command') {

                    if (Arr::exists($update['message']['from'], 'username')) {
                        $username = $update['message']['from']['username'];
                    } else {
                        $username = $update['message']['from']['first_name'];
                    }

                    if ($update['message']['text'] === "/" . self::ENTER) {

                        $participant = Participant::firstOrCreate([
                            'telegram_id' => $update['message']['from']['id']
                        ], [
                            'first_name' => $update['message']['from']['first_name'],
                            'last_name' => Arr::exists($update['message']['from'], 'last_name') ? $update['message']['from']['last_name'] : null,
                            'username' => $username
                        ]);

                        if ($participant->wasRecentlyCreated === true) {
                            $telegram->sendMessage(
                                ['chat_id' => $update['message']['chat']['id'],
                                    'text' => "Je doet nu mee aan de challenge, zet hem op!"]
                            );
                        } else {
                            $telegram->sendMessage(
                                ['chat_id' => $update['message']['chat']['id'],
                                    'text' => "Je was al opgegeven gekkie!"]
                            );
                        }

                    }

                    if ($update['message']['text'] === "/" . self::QUIT) {

                        $participant = Participant::where('telegram_id', $update['message']['from']['id'])->first();

                        if($participant) {
                            $participant->delete();

                            $telegram->sendMessage(
                                ['chat_id' => $update['message']['chat']['id'],
                                'text' => "Je doet nu niet meer mee, en al je stats zijn gewist"]
                            );
                        } else {
                            $telegram->sendMessage(
                                ['chat_id' => $update['message']['chat']['id'],
                                'text' => "Je doet nog niet mee mietje!"]
                            );
                        }

                    }

                    if ($update['message']['text'] === "/" . self::CHECK) {

                        $participant = Participant::where('telegram_id', $update['message']['from']['id'])->first();

                        if($participant)
                        {
                            $check = $participant->checks()->whereDate('checked_at', Carbon::today())->first();

                            if($check)
                            {
                                $telegram->sendMessage(
                                    ['chat_id' => $update['message']['chat']['id'],
                                    'text' => "Je hebt vandaag al koud gedouched, ben je dat vergeten?"]
                                );
                            } else {
                                Check::create([
                                    'participant_id' => $participant->id,
                                    'checked_at' => Carbon::now()
                                ]);

                                $telegram->sendMessage(
                                    ['chat_id' => $update['message']['chat']['id'],
                                    'text' => "Goed bezig! Staat genoteerd!"]
                                );
                            }
                        } else {
                            $telegram->sendMessage(
                                ['chat_id' => $update['message']['chat']['id'],
                                'text' => "Je doet nog niet mee, dus je kunt ook niet inchecken, typ /ikdoemee om mee te doen"]
                            );
                        }
                    }

                    if ($update['message']['text'] === "/" . self::CHECKS) {
                        $participant = Participant::where('telegram_id', $update['message']['from']['id'])->first();

                        if($participant) {

                            $checks = $participant->checks()->count();

                            $telegram->sendMessage(
                                ['chat_id' => $update['message']['chat']['id'],
                                'text' => "Je hebt nu ". $checks ." dag(en) koud afgedouched!"]
                            );
                        } else {
                            $telegram->sendMessage(
                                ['chat_id' => $update['message']['chat']['id'],
                                'text' => "Je doet nog niet mee, dus ik kan je dit niet vertellen"]
                            );
                        }
                    }

                } elseif ($update && Arr::exists($update, 'edited_message')) {
                    //
                } else {

//                    $telegram->sendMessage(
//                        ['chat_id' => $update['message']['chat']['id'],
//                            'text' => "Sorry, dit command ken ik niet"]
//                    );

                }
            }
        }
    }
}

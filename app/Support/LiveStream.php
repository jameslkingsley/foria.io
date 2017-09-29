<?php

namespace App\Support;

use OpenTok\Role;
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\ArchiveMode;
use App\Models\Broadcast;
use App\Exceptions\NoBroadcastException;

class LiveStream
{
    /**
     * The broadcast model instance.
     *
     * @var App\Models\Broadcast
     */
    protected $broadcast;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Gets the broadcast model instance.
     *
     * @return App\Models\Broadcast
     */
    public function getBroadcast()
    {
        return $this->broadcast ?: session('broadcast', null);
    }

    /**
     * Starts the live stream session.
     *
     * @return App\Models\Broadcast
     */
    public static function start(string $topic = 'Untitled')
    {
        $stream = new static;
        $archive = null;

        $sessionId = $stream->driver()
            ->createSession($stream->sessionOptions)
            ->getSessionId();

        if (config('opentok.archive', false)) {
            $archive = $stream->driver()->startArchive($sessionId);
        }

        $broadcast = auth()->user()->broadcasts()->save(
            new Broadcast([
                'online' => true,
                'topic' => $topic,
                'session_id' => $sessionId,
                'archive_id' => optional($archive)->id
            ])
        );

        $stream->broadcast = $broadcast;
        session(['broadcast' => $broadcast]);

        return $broadcast;
    }

    /**
     * Stops the live stream.
     *
     * @return mixed
     */
    public static function stop()
    {
        $stream = new static;
        $broadcast = $stream->getBroadcast();

        if (! $broadcast) {
            throw new NoBroadcastException;
        }

        $broadcast->update([
            'online' => false
        ]);

        if (config('opentok.archive', false)) {
            $stream->driver()->stopArchive(
                $broadcast->archive_id
            );
        }

        session(['broadcast' => null]);
    }

    /**
     * Gets a session token with the given role.
     *
     * @return string
     */
    public static function token(Broadcast $broadcast, $role)
    {
        $stream = new static;

        return $stream->driver()->generateToken(
            $broadcast->session_id,
            ['role' => $role]
        );
    }
}

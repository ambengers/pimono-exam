import echo from '../echo';

export function useEcho() {
    /**
     * Listen to a public channel
     */
    function channel(channelName: string) {
        return echo.channel(channelName);
    }

    /**
     * Listen to a private channel
     */
    function privateChannel(channelName: string) {
        return echo.private(channelName);
    }

    /**
     * Listen to a presence channel
     */
    function presenceChannel(channelName: string) {
        return echo.join(channelName);
    }

    /**
     * Leave a channel
     */
    function leaveChannel(channelName: string) {
        echo.leave(channelName);
    }

    /**
     * Disconnect from Echo
     */
    function disconnect() {
        echo.disconnect();
    }

    /**
     * Get the Echo instance
     */
    function getEcho() {
        return echo;
    }

    return {
        channel,
        privateChannel,
        presenceChannel,
        leaveChannel,
        disconnect,
        getEcho,
    };
}


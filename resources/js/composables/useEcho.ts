import echo from '../echo';

export function useEcho() {
    function privateChannel(channelName: string) {
        return echo.private(channelName);
    }

    function leaveChannel(channelName: string) {
        echo.leave(channelName);
    }

    return {
        privateChannel,
        leaveChannel,
    };
}


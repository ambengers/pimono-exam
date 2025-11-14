export function useDate() {
    function formatDate(dateString: string): string {
        const date = new Date(dateString);
        const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: timeZone,
        }).format(date);
    }

    return {
        formatDate,
    };
}


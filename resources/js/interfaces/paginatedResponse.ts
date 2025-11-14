export interface PaginatedResponse<T> {
    data: T[];
    meta: {
        path: string;
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    },
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
}


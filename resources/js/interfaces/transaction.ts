export interface Transaction {
    id: number;
    sender_id: number;
    receiver_id: number;
    amount: number;
    commission_fee: number;
    commission_fee_percentage: number;
    total: number;
    sender_balance_before: number;
    sender_balance_after: number;
    receiver_balance_before: number;
    receiver_balance_after: number;
    created_at: string;
    updated_at: string;
    sender?: {
        id: number;
        name: string;
        email: string;
    };
    receiver?: {
        id: number;
        name: string;
        email: string;
    };
}


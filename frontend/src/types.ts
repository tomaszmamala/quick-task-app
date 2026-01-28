export interface Task {
    id: number;
    title: string;
    description: string | null;
    status: boolean;
    priority: number;
    createdAt: string;
}

export interface NewTask {
    title: string;
    description: string;
    priority: number;
}

import { IDataTable } from "./global";

export interface IEvent {
    id: string; // id_hash
    name: string;
    slug: string;
    date: string;
    location: string;
    description: string;
}

export interface IDataTableEvent extends IDataTable {
    data: IEvent[];
}

export interface IEventDetail extends IEvent {
    createdAt: string;
}

export interface ICreateEventPayload {
    name: string;
    date: string;
    location: string;
    description: string;
}

export interface IEditEventPayload {
    name: string;
    date: string;
    location: string;
    description: string;
}
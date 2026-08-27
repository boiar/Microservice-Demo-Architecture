import { InboxEvent } from "../entity/inbox-event.entity";

export interface InboxInterface {
    isProcessed(eventId: string): Promise<boolean>;

    save(
        eventId: string,
        eventType: string,
        payload: object,
    ): Promise<InboxEvent>;
}
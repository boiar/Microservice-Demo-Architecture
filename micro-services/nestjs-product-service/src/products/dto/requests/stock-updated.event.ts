import { StockUpdatedEventType } from "src/products/enum/stock-updated-event-type.enum";

export class StockUpdatedEvent {
    eventId: string;
    eventType: StockUpdatedEventType;
    productId: number;
    availableQty: number;
}

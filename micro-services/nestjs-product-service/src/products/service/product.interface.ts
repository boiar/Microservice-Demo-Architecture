import { StockUpdatedEvent } from "../dto/requests/stock-updated.event";
import { ProductResponse } from "../dto/responses/product-response.dto";

export interface IProductInterface {

    findAll(): Promise<ProductResponse[]>;

    findOne(id: number): Promise<ProductResponse>;

    findByIds(ids: number[]): Promise<ProductResponse[]>;

    handleStockUpdatedEvent(stockUpdatedEvent: StockUpdatedEvent): Promise<void>;
}
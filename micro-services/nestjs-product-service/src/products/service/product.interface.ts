import { StockUpdatedEvent } from "../dto/requests/stock-updated.event";
import { Product } from "../entity/product.entity";

export interface IProductInterface {

    findAll(): Promise<Product[]>;

    findOne(id: number): Promise<Product>;

    handleStockUpdatedEvent(stockUpdatedEvent: StockUpdatedEvent): Promise<void>;
}
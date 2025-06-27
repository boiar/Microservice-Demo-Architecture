import {Product} from "./product.entity";

export interface IProductInterface {

    findAll(): Promise<Product[]>;

    findOne(id: string): Promise<Product>;

    decreaseProductQty(
        products: { product_id: number; qty: number }[],
        orderId?: number,
        userId?: number,
        userEmail?: string,
    ): Promise<void>;
}
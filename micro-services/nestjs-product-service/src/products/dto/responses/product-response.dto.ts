import { Product } from "../../entity/product.entity";

export class ProductResponse {

    id: number;
    name: string;
    description?: string;
    price: number;
    availableQty: number;
    createdAt: Date;
    updatedAt: Date;

    constructor(partial: Partial<ProductResponse>) {
        Object.assign(this, partial);
    }

    static fromEntity(product: Product): ProductResponse {
        return new ProductResponse({
            id: product.id,
            name: product.name,
            description: product.description,
            price: product.price,
            availableQty: product.availableQty,
        });
    }

    static fromEntities(products: Product[]): ProductResponse[] {
        return products.map((product) => ProductResponse.fromEntity(product));
    }
    
}
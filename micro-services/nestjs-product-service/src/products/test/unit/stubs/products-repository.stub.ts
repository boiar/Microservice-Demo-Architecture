import { IProductRepositoryInterface } from "../../../interfaces/product-repository.interface";
import { Product } from "../../../product.entity";
import { FindManyOptions, FindOneOptions} from "typeorm";
import {FindOptionsWhere} from "typeorm/find-options/FindOptionsWhere";

export class ProductsRepositoryStub implements IProductRepositoryInterface {
    private products: Product[] = [
        {
            id: 1,
            name: "Test Product 1",
            qty: 10,
            price: 100,
            description: "Test Desc 1"
        },
        {
            id: 2,
            name: "Test Product 2",
            qty: 20,
            price: 200,
            description: "Test Desc 2"
        },
    ];

    async create(data: Partial<Product>): Promise<Product> {
        const newProduct: Product = {
            id: this.products.length + 1,
            ...data,
        } as Product;

        this.products.push(newProduct);
        return newProduct;
    }

    async delete(id: number): Promise<void> {
        this.products = this.products.filter(p => p.id !== id);
    }

    async findAll(options?: FindManyOptions<Product>): Promise<Product[]> {
        return this.products;
    }

    async findById(id: number): Promise<Product | null> {
        return this.products.find(p => p.id === id) || null;
    }

    async findOne(options: FindOneOptions<Product>): Promise<Product | null> {
        const where = options.where as FindOptionsWhere<Product>;
        return this.products.find(p => Object.entries(where).every(([key, value]) => p[key] === value)) || null;
    }

    async findOneBy(where: FindOptionsWhere<Product> | FindOptionsWhere<Product>[]): Promise<Product | null> {
        if (Array.isArray(where)) {
            for (const clause of where) {
                const found = this.products.find(p => Object.entries(clause).every(([key, value]) => p[key] === value));
                if (found) return found;
            }
            return null;
        } else {
            return this.products.find(p => Object.entries(where).every(([key, value]) => p[key] === value)) || null;
        }
    }

    async save(data: Product): Promise<Product> {
        const index = this.products.findIndex(p => p.id === data.id);
        if (index !== -1) {
            this.products[index] = data;
        } else {
            this.products.push(data);
        }
        return data;
    }

    async update(id: number, product: Partial<Product>): Promise<Product> {
        const existing = await this.findById(id);
        if (!existing) {
            throw new Error("Product not found");
        }

        const updated = { ...existing, ...product };
        this.products = this.products.map(p => (p.id === id ? updated : p));
        return updated as Product;
    }

    async decrementQty(p: { id: number }, qty: number): Promise<void> {
        const product = this.products.find(prod => prod.id === p.id);
        if (!product) {
            throw new Error(`Product with id ${p.id} not found`);
        }
        if (product.qty < qty) {
            throw new Error(`Insufficient quantity for product with id ${p.id}`);
        }

        product.qty -= qty;
    }
}

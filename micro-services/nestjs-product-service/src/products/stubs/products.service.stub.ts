import { IProductInterface } from '../iproduct.interface';
import { Product } from '../product.entity';

export class StubProductsService implements IProductInterface {
    public products: Product[] = [
        {
            id: 1,
            name: 'Laptop Stub',
            qty: 10,
            price: 1200,
            description: 'Laptop Stub Description'
        },
        {
            id: 2,
            name: 'Mouse Stub',
            qty: 50,
            price: 25,
            description: 'Mouse Stub Description'
        },
        {
            id: 3,
            name: 'Keyboard Stub',
            qty: 30,
            price: 75,
            description: 'Keyboard Stub Description'
        },
    ];

    findAll(): Promise<Product[]> {
        return Promise.resolve([...this.products]);
    }

    // @ts-ignore
    findOne(id: number): Promise<Product | undefined> {
        return Promise.resolve(this.products.find(p => p.id === id));
    }

    decreaseProductQty = jest.fn(); // mock for tests
}

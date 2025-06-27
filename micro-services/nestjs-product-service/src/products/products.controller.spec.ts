import {ProductsController} from "./products.controller";
import {ProductsService} from "./products.service";
import {IProductInterface} from "./iproduct.interface";
import {Test, TestingModule} from "@nestjs/testing";
import {StubProductsService} from "./stubs/products.service.stub";
import {Product} from "./product.entity";
import {NotFoundException} from "@nestjs/common";

describe('ProductsController', () => {

    let controller: ProductsController;
    let service: IProductInterface;

    beforeEach(async () => {

        const module: TestingModule = await Test.createTestingModule({
            controllers: [ProductsController],
            providers: [
                {
                    provide: ProductsService,
                    useClass: StubProductsService,
                },
            ],
        }).compile();


        controller = module.get<ProductsController>(ProductsController);
        service    = module.get<IProductInterface>(ProductsService);
    })

    it('should be defined', () => {
        expect(controller).toBeDefined();
    })


    describe('getProducts', () => {
        it('should return a list of products and call findAll once', async () => {
            const findAllSpy = jest.spyOn(service, 'findAll');

            const result = await controller.getProducts();

            expect(result).toHaveLength(3);
            expect(findAllSpy).toHaveBeenCalledTimes(1);
        });
    });
    
    describe('findOne', () => {

        it('should return one product if found', async () => {

            const productId = 1;
            const product= await controller.findOne(productId);

            expect(product).toEqual({ id: 1, name: 'Laptop Stub', qty: 10, price: 1200,  description: 'Laptop Stub Description' });
        });

        it('should throw NotFoundException if product not found', async () => {
            await expect(controller.findOne(999)).rejects.toThrow(NotFoundException);
        });

    })

})
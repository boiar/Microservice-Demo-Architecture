import {ProductsController} from "../../products.controller";
import {ProductsService} from "../../products.service";
import {IProductInterface} from "../../iproduct.interface";
import {Test, TestingModule} from "@nestjs/testing";
import {NotFoundException} from "@nestjs/common";
import {ProductsRepositoryStub} from "../unit/stubs/products-repository.stub";
import {CACHE_MANAGER} from "@nestjs/cache-manager";

describe('ProductsController', () => {

    let controller: ProductsController;
    let service: IProductInterface;
    let cache: Cache;

    const mockCache = {
        get: jest.fn(),
        set: jest.fn(),
        del: jest.fn(),
    };

    beforeEach(async () => {

        const module: TestingModule = await Test.createTestingModule({
            controllers: [ProductsController],
            providers: [
                ProductsService,
                ProductsService,
                {
                    provide: 'ProductRepository',
                    useClass: ProductsRepositoryStub,
                },
                {
                    provide: CACHE_MANAGER,
                    useValue: mockCache,
                },

            ],
        }).compile();


        controller = module.get<ProductsController>(ProductsController);
        service    = module.get<IProductInterface>(ProductsService);
        cache = module.get<Cache>(CACHE_MANAGER);

    })

    it('should be defined', () => {
        expect(controller).toBeDefined();
    })


    describe('getProducts', () => {
        it('should return a list of products and call findAll once', async () => {
            const findAllSpy = jest.spyOn(service, 'findAll');

            const result = await controller.getProducts();

            expect(result).toHaveLength(2);
            expect(findAllSpy).toHaveBeenCalledTimes(1);
        });
    });
    
    describe('findOne', () => {

        it('should return one product if found', async () => {

            const productId = 1;
            const product= await controller.findOne(productId);

            expect(product).toEqual({ id: 1, name: 'Test Product 1', qty: 10, price: 100,  description: 'Test Desc 1' });
        });

        it('should throw NotFoundException if product not found', async () => {
            await expect(controller.findOne(999)).rejects.toThrow(NotFoundException);
        });

    })

})
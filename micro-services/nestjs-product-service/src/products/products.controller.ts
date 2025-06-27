import {Controller, Get, NotFoundException, Param, ParseIntPipe, UseGuards} from '@nestjs/common';
import {ProductsService} from "./products.service";
import {Product} from "./product.entity";
import {JwtAuthGuard} from "../auth/jwt-auth.guard";

@Controller('products')
export class ProductsController {

    constructor(private readonly productsService: ProductsService) {}

    @UseGuards(JwtAuthGuard)
    @Get()
    async getProducts() {
        return this.productsService.findAll();
    }

    @UseGuards(JwtAuthGuard)
    @Get(':id')
    async findOne(@Param('id', ParseIntPipe) id: number): Promise<Product> {
        const product = await this.productsService.findOne(id);
        if (!product) {
            throw new NotFoundException(`Product with ID ${id} not found`);
        }
        return product;
    }
}

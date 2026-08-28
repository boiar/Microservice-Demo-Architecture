import { Controller, Get, NotFoundException, Param, ParseIntPipe, UseGuards, Logger } from '@nestjs/common';
import { ProductsService } from "../service/impl/products.service";
import { JwtAuthGuard } from "../../auth/jwt-auth.guard";
import { EventPattern, Payload } from '@nestjs/microservices';
import { StockUpdatedEvent } from '../dto/requests/stock-updated.event';
import { ProductEvent } from '../enum/product-event.enum';
import { ProductResponse } from '../dto/responses/product-response.dto';

@Controller('products')
export class ProductsController {

    private readonly logger = new Logger(ProductsController.name);

    constructor(private readonly productsService: ProductsService) { }

    @UseGuards(JwtAuthGuard)
    @Get()
    async getProducts(): Promise<ProductResponse[]> {
        return this.productsService.findAll();
    }

    @UseGuards(JwtAuthGuard)
    @Get(':id')
    async findOne(@Param('id', ParseIntPipe) id: number): Promise<ProductResponse> {
        const product = await this.productsService.findOne(id);
        if (!product) {
            throw new NotFoundException(`Product with ID ${id} not found`);
        }
        return product;
    }

    @EventPattern(ProductEvent.STOCK_UPDATED)
    async handleStockUpdated(
        @Payload() event: StockUpdatedEvent,
    ): Promise<void> {
        this.logger.log(
            `Received stock.updated event for product ${event.productId}`,
        );

        await this.productsService.handleStockUpdatedEvent(event);
    }
}

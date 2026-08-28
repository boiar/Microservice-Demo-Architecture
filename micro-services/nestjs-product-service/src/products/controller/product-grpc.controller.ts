import { Controller } from "@nestjs/common";
import { GrpcMethod } from "@nestjs/microservices";
import { ProductsService } from "../service/impl/products.service";

interface GetProductByIdRequest {
  id: number;
}

interface ProductResponse {
  found: boolean;
  id: number;
  name: string;
  price: number;
  availableQty: Number;
}
interface ProductsByIdsRequest {
  ids: number[];
}

interface ProductsByIdsResponse {
  products: ProductResponse[];
}

@Controller()
export class ProductGrpcController {
  constructor(private readonly productsService: ProductsService) {}

  @GrpcMethod('ProductService', 'GetProductById')
  async getProductById(data: GetProductByIdRequest): Promise<ProductResponse> {
    const product = await this.productsService.findOne(data.id);

    if (!product) {
      return { found: false, id: 0, name: '', price: 0 };
    }

    return {
      found: true,
      id: product.id,
      name: product.name,
      price: Number(product.price),
      availableQty: Number(product.availableQty)
    };
  }

  @GrpcMethod('ProductService', 'GetProductsByIds')
  async getProductsByIds(data: ProductsByIdsRequest): Promise<ProductsByIdsResponse> {
    const products = await this.productsService.findByIds(data.ids || []);
    
    return {
      products: products.map(product => ({
        found: true,
        id: product.id,
        name: product.name,
        price: Number(product.price),
        availableQty: Number(product.availableQty)
      }))
    };
  }
}
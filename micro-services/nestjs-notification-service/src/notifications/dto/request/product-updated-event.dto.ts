export class ProductUpdatedEventDto {
  user_id: string | number;
  user_email: string;
  product_id: string | number;
  timestamp?: string;
}
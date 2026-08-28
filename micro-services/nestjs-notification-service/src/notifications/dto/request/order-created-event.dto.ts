export class OrderCreatedEventDto {
  user_id: string | number;
  user_email: string;
  order_id: string | number;
  timestamp?: string;
}
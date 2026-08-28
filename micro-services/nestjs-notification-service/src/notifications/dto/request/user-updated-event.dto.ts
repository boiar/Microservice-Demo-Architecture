
export class UserUpdatedEventDto {
  data: {
    id: string | number;
    name?: string;
    email: string;
  };

  timestamp?: string;
}
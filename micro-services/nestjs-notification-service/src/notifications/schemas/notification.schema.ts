import { Prop, Schema, SchemaFactory } from '@nestjs/mongoose';
import { Document } from 'mongoose';


@Schema()
export class Notification extends Document {
    @Prop({ required: true })
    userId: string; // Store as string, as Laravel BigInt might not map directly

    @Prop({ required: true })
    userEmail: string;

    @Prop({ required: true })
    type: string; // e.g., 'welcome_email'

    @Prop({ required: true })
    message: string;

    @Prop({ default: Date.now })
    timestamp: Date;

    @Prop({ default: 'pending' })
    status: string; // e.g., 'sent', 'failed'
}

export const NotificationSchema = SchemaFactory.createForClass(Notification);
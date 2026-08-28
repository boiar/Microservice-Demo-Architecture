import { Injectable } from "@nestjs/common";
import { InjectRepository } from "@nestjs/typeorm";
import { InboxEvent } from "src/products/entity/inbox-event.entity";
import { InboxInterface } from "../inbox.interface";
import { Repository } from "typeorm";

@Injectable()
export class InboxService implements InboxInterface {

    constructor(
        @InjectRepository(InboxEvent)
        private readonly inboxRepository: Repository<InboxEvent>,
    ) {}

    async isProcessed(eventId: string): Promise<boolean> {

        const event = await this.inboxRepository.findOne({
            where: {
                eventId,
            },
        });

        return !!event;
    }

    async save(
        eventId: string,
        eventType: string,
        payload: object,
    ): Promise<InboxEvent> {

        const inboxEvent = this.inboxRepository.create({
            eventId,
            eventType,
            payload,
            processedAt: new Date(),
        });

        return this.inboxRepository.save(inboxEvent);
    }
}
<?php

namespace App\Services;

use App\Contracts\Services\IProductGrpcService;
use Grpc\BaseStub;
use Grpc\ChannelCredentials;
use Illuminate\Support\Facades\Log;

/**
 * gRPC client for nestjs-product-service.
 *
 * This stub is hand-written to avoid requiring protoc/protobuf codegen on the host.
 * It sends raw gRPC calls matching product.proto definitions.
 *
 * proto package  : products
 * proto service  : ProductService
 * Methods        : GetProductById, GetProductsByIds
 */
class ProductGrpcClient extends BaseStub
{
    public function __construct(string $hostname, array $opts, $channel = null)
    {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Call GetProductById rpc.
     * We serialize the request manually using protobuf binary wire format.
     *
     * Field 1 (id) = int32 → wire type 0 (varint)
     */
    public function GetProductById(int $id): array
    {
        // Encode field 1 as varint: tag = (1 << 3) | 0 = 0x08
        $request = chr(0x08) . self::encodeVarint($id);

        return $this->_simpleRequest(
            '/products.ProductService/GetProductById',
            $request,
            function (string $data) { return self::decodeProductResponse($data); },
            [],
            []
        )->wait();
    }

    /**
     * Call GetProductsByIds rpc.
     *
     * Field 1 (ids) = repeated int32 → wire type 0 (varint), tag = 0x08 per element
     */
    public function GetProductsByIds(array $ids): array
    {
        $request = '';
        foreach ($ids as $id) {
            $request .= chr(0x08) . self::encodeVarint((int) $id);
        }

        return $this->_simpleRequest(
            '/products.ProductService/GetProductsByIds',
            $request,
            function (string $data) { return self::decodeProductsByIdsResponse($data); },
            [],
            []
        )->wait();
    }

    // ---------------------------------------------------------------
    // Minimal protobuf helpers (no protoc required)
    // ---------------------------------------------------------------

    private static function encodeVarint(int $value): string
    {
        $bytes = '';
        while ($value > 0x7F) {
            $bytes .= chr(($value & 0x7F) | 0x80);
            $value >>= 7;
        }
        $bytes .= chr($value & 0x7F);
        return $bytes;
    }

    private static function decodeVarint(string $data, int &$offset): int
    {
        $result = 0;
        $shift  = 0;
        do {
            if ($offset >= strlen($data)) break;
            $byte    = ord($data[$offset++]);
            $result |= ($byte & 0x7F) << $shift;
            $shift  += 7;
        } while ($byte & 0x80);
        return $result;
    }

    /**
     * Decode a single ProductResponse message.
     *
     * Proto fields:
     *   1 id            int32
     *   2 name          string
     *   3 description   string
     *   4 price         float
     *   5 available_qty int32
     */
    public static function decodeProductResponse(string $data): array
    {
        $result = ['id' => 0, 'name' => '', 'description' => '', 'price' => 0.0, 'available_qty' => 0];
        $len    = strlen($data);
        $offset = 0;

        while ($offset < $len) {
            $tag       = self::decodeVarint($data, $offset);
            $fieldNum  = $tag >> 3;
            $wireType  = $tag & 0x07;

            switch ($fieldNum) {
                case 1: // id (varint)
                    $result['id'] = self::decodeVarint($data, $offset);
                    break;
                case 2: // name (length-delimited)
                    $strLen = self::decodeVarint($data, $offset);
                    $result['name'] = substr($data, $offset, $strLen);
                    $offset += $strLen;
                    break;
                case 3: // description (length-delimited)
                    $strLen = self::decodeVarint($data, $offset);
                    $result['description'] = substr($data, $offset, $strLen);
                    $offset += $strLen;
                    break;
                case 4: // price (float, wire type 5)
                    $result['price'] = unpack('f', substr($data, $offset, 4))[1];
                    $offset += 4;
                    break;
                case 5: // available_qty (varint)
                    $result['available_qty'] = self::decodeVarint($data, $offset);
                    break;
                default:
                    // Skip unknown fields
                    if ($wireType === 0) self::decodeVarint($data, $offset);
                    elseif ($wireType === 2) { $l = self::decodeVarint($data, $offset); $offset += $l; }
                    elseif ($wireType === 5) $offset += 4;
                    elseif ($wireType === 1) $offset += 8;
                    break;
            }
        }

        return $result;
    }

    /**
     * Decode a ProductsByIdsResponse which contains repeated ProductResponse messages.
     *
     * Proto field:
     *   1 products  repeated ProductResponse (length-delimited)
     */
    private static function decodeProductsByIdsResponse(string $data): array
    {
        $products = [];
        $len      = strlen($data);
        $offset   = 0;

        while ($offset < $len) {
            $tag      = self::decodeVarint($data, $offset);
            $fieldNum = $tag >> 3;
            $wireType = $tag & 0x07;

            if ($fieldNum === 1 && $wireType === 2) {
                $msgLen   = self::decodeVarint($data, $offset);
                $msgBytes = substr($data, $offset, $msgLen);
                $offset  += $msgLen;
                $products[] = self::decodeProductResponse($msgBytes);
            } else {
                // Skip unknown fields
                if ($wireType === 0) self::decodeVarint($data, $offset);
                elseif ($wireType === 2) { $l = self::decodeVarint($data, $offset); $offset += $l; }
                elseif ($wireType === 5) $offset += 4;
                elseif ($wireType === 1) $offset += 8;
            }
        }

        return ['products' => $products];
    }
}


class ProductGrpcService implements IProductGrpcService
{
    protected ?ProductGrpcClient $client = null;

    public function __construct()
    {
        $host = env('PRODUCT_GRPC_HOST', 'nestjs-product-service:50051');

        try {
            $this->client = new ProductGrpcClient($host, [
                'credentials' => ChannelCredentials::createInsecure(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("Could not initialize gRPC client: " . $e->getMessage());
            $this->client = null;
        }
    }

    /**
     * Fetch a single product by ID via gRPC.
     */
    public function getProductById(int $id): ?object
    {
        if (!$this->client) {
            Log::error("gRPC client not initialized. Check PRODUCT_GRPC_HOST and grpc extension.");
            return null;
        }

        try {
            [$response, $status] = $this->client->GetProductById($id);

            if ($status->code !== \Grpc\STATUS_OK) {
                Log::error("gRPC GetProductById error [{$status->code}]: {$status->details}");
                return null;
            }

            if (empty($response) || empty($response['id'])) {
                return null;
            }

            return (object) $response;

        } catch (\Throwable $e) {
            Log::error("gRPC GetProductById exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch multiple products by IDs via gRPC.
     */
    public function getProductsByIds(array $ids): ?array
    {
        if (!$this->client) {
            Log::error("gRPC client not initialized. Check PRODUCT_GRPC_HOST and grpc extension.");
            return null;
        }

        try {
            [$response, $status] = $this->client->GetProductsByIds($ids);

            if ($status->code !== \Grpc\STATUS_OK) {
                Log::error("gRPC GetProductsByIds error [{$status->code}]: {$status->details}");
                return null;
            }

            return array_map(
                fn($p) => (object) $p,
                $response['products'] ?? []
            );

        } catch (\Throwable $e) {
            Log::error("gRPC GetProductsByIds exception: " . $e->getMessage());
            return null;
        }
    }
}

<?php namespace ExcelBKK;

use Illuminate\Support\Facades\Response as BaseResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;

class Response extends BaseResponse {

  // ---------------------------------------------------------------------------
  // Configuration

  const DEFAULT_OFFSET = 0;
  const DEFAULT_LIMIT = 20;

  // ---------------------------------------------------------------------------
  // Response

  public static function api($message, $status = 200, $data = NULL, $has_pagination = FALSE, $headers = array(), $options = 0) {
    if ($data instanceof ArrayableInterface) {
      $data = $data->toArray();
    }

    $response_data = array(
      'meta_data' => self::_buildMetaData($message, $status, $has_pagination),
      'data' => $data,
    );

    return new JsonResponse($response_data, $status, $headers, $options);
  }

  // ---------------------------------------------------------------------------
  // Meta data

  protected static function _buildMetaData($message = NULL, $status = 200, $has_pagination = FALSE) {
    return array(
      'request_params' => json_decode(json_encode( Input::all(), JSON_NUMERIC_CHECK )),
      'success' => $status == 200 ? $message : NULL,
      'errors' => $status != 200 ? $message : NULL,
      'next_page' => $has_pagination ? self::_buildPagination('next_page') : NULL,
      'previous_page' => $has_pagination ? self::_buildPagination('previous_page') : NULL,
    );
  }

  // ---------------------------------------------------------------------------
  // Pagination

  protected static function _buildPagination($type) {
    $queries = Input::all();

    $queries['limit'] = empty($queries['limit']) ? self::DEFAULT_LIMIT : $queries['limit'];

    switch ($type) {
      case 'next_page':
        $queries['offset'] = empty($queries['offset']) ?
          self::DEFAULT_OFFSET + $queries['limit'] : $queries['offset'] + $queries['limit'];
        break;
      case 'previous_page':
        $queries['offset'] = empty($queries['offset']) ?
          self::DEFAULT_OFFSET + $queries['limit'] : $queries['offset'] - $queries['limit'];
        break;
    }

    return Request::url() . '?' . http_build_query($queries);
  }

}
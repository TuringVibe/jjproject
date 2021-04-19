<?php
namespace App\Http\Controllers;

use App\Http\Requests\ValidateEvent;
use App\Http\Requests\ValidateEventId;
use App\Http\Requests\ValidateEventParams;
use App\Services\EventService;

class EventController extends Controller {

    private $event_service;

    public function __construct(EventService $event_service)
    {
        $this->event_service = $event_service;
    }

    public function list() {
        $this->config['title'] = "CALENDAR";
        $this->config['active'] = "events.list";
        $this->config['navs'] = [
            [
                'label' => 'Calendar'
            ]
        ];
        return view('pages.calendar-list', $this->config);
    }

    public function data(ValidateEventParams $request) {
        $params = $request->validated();
        unset($params['tz']);
        $result = $this->event_service->get($request->tz, $params);
        return $result->toArray();
    }

    public function edit(ValidateEventId $request) {
        $result = $this->event_service->detail($request->id);
        return $result;
    }

    public function save(ValidateEvent $request) {
        $result = $this->event_service->save($request->validated());
        if($result) {
            return [
                'status' => true,
                'message' => 'Data saved successfully',
                'data' => $result
            ];
        }
        return [
            'status' => false,
            'message' => 'Failed to save data'
        ];
    }

    public function delete(ValidateEventId $request) {
        $result = $this->event_service->delete($request->id);
        if($result) {
            return [
                'status' => true,
                'message' => 'Data deleted successfully'
            ];
        }
        return [
            'status' => false,
            'message' => 'Failed to delete data'
        ];
    }
}

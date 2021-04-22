<?php
namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EventService {

    public function get($tz = null, $params = []) {
        $query_builder = Event::whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)){
                switch($field) {
                    case 'date_from':
                        $query_builder->where('startdatetime','>=',$val);
                        $query_builder->where('enddatetime','>=',$val);
                        break;
                    case 'date_to':
                        $query_builder->where('startdatetime','<=',$val);
                        $query_builder->where('enddatetime','<=',$val);
                        break;
                }
            }
        }
        $query_builder->orderBy('startdatetime','asc');

        $events = collect();
        foreach($query_builder->cursor() as $event) {
            $event_item = [
                'id' => $event->id,
                'title' => $event->name,
                'start' => $event->startdatetime->format('Y-m-d H:i:s'),
                'end' => $event->enddatetime->format('Y-m-d H:i:s')
            ];
            if($event->repeat != 'once') {
                $event_item['groupId'] = $event->id;
                $event_item['rrule'] = [
                    'freq' => $event->repeat,
                    'dtstart' => $event->startdatetime->format('Y-m-d H:i:s'),
                    'tzid' => $tz
                ];
                $event_item['duration'] = [
                    'minutes' => $event->startdatetime->diffInMinutes($event->enddatetime)
                ];

                switch($event->repeat) {
                    case 'daily':
                        $event_item['backgroundColor'] = '#fcc603   ';
                        $event_item['borderColor'] = '#fcc603';
                        break;
                    case 'weekly':
                        $event_item['backgroundColor'] = '#00cf0e';
                        $event_item['borderColor'] = '#00cf0e';
                        break;
                    case 'biweekly':
                        $event_item['rrule']['freq'] = 'weekly';
                        $event_item['rrule']['interval'] = 2;
                        $event_item['backgroundColor'] = '#03bafc';
                        $event_item['borderColor'] = '#03bafc';
                    break;
                    case 'monthly':
                        $event_item['backgroundColor'] = '#ba03fc';
                        $event_item['borderColor'] = '#ba03fc';
                    break;
                    case 'yearly':
                        $event_item['backgroundColor'] = '#fc0341';
                        $event_item['borderColor'] = '#fc0341';
                    break;
                }
            }
            $events->push($event_item);
        }

        return $events;
    }

    public function detail($id) {
        $event = Event::find($id);
        return $event;
    }

    public function save($attr) {
        if(isset($attr['id'])) {
            $event = Event::find($attr['id']);
            $attr['updated_by'] = Auth::user()->id;
            $event->fill($attr);
            $event->save();
        } else {
            $attr['created_by'] = Auth::user()->id;
            $attr['updated_by'] = $attr['created_by'];
            $event = Event::create($attr);
        }
        return $event;
    }

    public function delete($id) {
        $event = Event::find($id);
        $event->deleted_by = Auth::user()->id;
        $event->deleted_at = Carbon::now();
        return $event->save();
    }

}

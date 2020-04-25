<?php

namespace App\Listeners;

use App\Events\ReadingInserted;
use App\Alert;
use App\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Notifications\ReadingAlert;

class ReadingAlertTrigger
{

    public $user;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReadingInserted  $event
     * @return void
     */
    public function handle(ReadingInserted $event)
    {
        $this->user = ($event->user);
        Log::debug('[Reading inserted] user.id =>'. $event->user->id);
        
        Log::debug('$event->user->alerts =>'. $event->user->alerts);
        $this->checkReportCreation($event->reading);
        foreach($event->user->alerts as $alert){
            Log::debug('checking trigger: '. $alert);
            $this->checkTrigger($alert, $event->reading);
        } 
    }

    public function checkReportCreation($reading){
        $months =  array('january','february','march','april','may','june','july ','august','september','october','november','december');

        if(!Report::where('year',$reading->calc_year)->where('month', $months[$reading->calc_month-1])->where('user_id',$reading->device->user_id)->exists()){
            Log::debug('New Report created!: ');
            $report = new Report;
            $report->year = $reading->calc_year;
            $report->month = $months[$reading->calc_month-1];
            $report->user_id = $reading->device->user_id;
            $report->time = $reading->time;
            $report->save();
            Log::debug('Report Debug: '. $report);
        }else{
            Log::Debug('Report already exists!: ');        
        }
    }

    public function checkTrigger(Alert $alert, $reading){
        Log::debug('Checking Trigger => '. $alert->name);
        $readingUnitValue = [-1, -1, -1];
        switch ($alert->unit) {
            case 'voltage':
                $readingUnitValue[0] = $reading->v1;
                $readingUnitValue[1] = $reading->v2;
                $readingUnitValue[2] = $reading->v3;
            break;
            case 'current':
                $readingUnitValue[0] = $reading->i1;
                $readingUnitValue[1] = $reading->i2;
                $readingUnitValue[2] = $reading->i3;
            break;
            case 'apparent power':
                $readingUnitValue[0] = $reading->p1;
                $readingUnitValue[1] = $reading->p2;
                $readingUnitValue[2] = $reading->p3;
            break;
            case 'active power':
                $readingUnitValue[0] = $reading->a1;
                $readingUnitValue[1] = $reading->a2;
                $readingUnitValue[2] = $reading->a3;
            break;
            case 'frequency':
                $readingUnitValue[0] = $reading->q1;
                $readingUnitValue[1] = $reading->q2;
                $readingUnitValue[2] = $reading->q3;
            break;
            case 'power factor':
                $readingUnitValue[0] = $reading->f1;
                $readingUnitValue[1] = $reading->f2;
                $readingUnitValue[2] = $reading->f3;
            break;
        }
        if($readingUnitValue[0] == -1 || $readingUnitValue[1] == -1 || $readingUnitValue[2] == -1){
            Log::debug('Checking Trigger returned false');
            return false;
        }
        if(!$this->checkCondition($alert, $readingUnitValue)){
            Log::debug('    Alert Not Fired, condition not met => '. $alert->name);
        }
    }

    public function checkCondition(Alert $alert, $readingUnitValue){

        Log::debug('Checking Condition...');

        switch ($alert->condition) {
            case 'bigger than':
                if($readingUnitValue[0] > $alert->threshold || $readingUnitValue[1] > $alert->threshold || $readingUnitValue[2] > $alert->threshold){
                    $this->fireAlert($alert, $readingUnitValue);
                    return true;
                }
            return false;
            case 'lesser than':
                if($readingUnitValue[0] < $alert->threshold || $readingUnitValue[1] < $alert->threshold || $readingUnitValue[2] < $alert->threshold){
                    $this->fireAlert($alert, $readingUnitValue);
                    return true;
                }
            return false;
            case 'between':
                if(
                    ($readingUnitValue[0] > $alert->threshold && $readingUnitValue[0] < $alert->threshold2) || 
                    ($readingUnitValue[1] > $alert->threshold && $readingUnitValue[1] < $alert->threshold2) || 
                    ($readingUnitValue[2] > $alert->threshold && $readingUnitValue[2] < $alert->threshold2) ){
                        $this->fireAlert($alert, $readingUnitValue);
                        return true;
                }
            return false;
            case 'equal':
                if($readingUnitValue[0] == $alert->threshold || $readingUnitValue[1] == $alert->threshold || $readingUnitValue[2] == $alert->threshold){
                    $this->fireAlert($alert, $readingUnitValue);
                    return true;
                }
            return false;
        }
    }

    public function fireAlert(Alert $alert, $readingUnitValue){
        $sendMail = ($alert->type == "email") ? true : false;
        Log::debug('    Alert Fired => '. $alert->name . '; *** $sendMail = '. $sendMail);
        $this->user->notify(new ReadingAlert($alert, $sendMail, $readingUnitValue));
    }
}

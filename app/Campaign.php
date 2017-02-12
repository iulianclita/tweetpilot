<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Website;

class Campaign extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'campaigns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'type',
    	'status',
        'custom_message',
    	'custom_link',
    	'post_id',
    	'resume_token',
    	'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * The number of workers associated with any campaign.
     *
     * @var int
     */
    const NUM_WORKERS = 1;

    /**
     * Create a new campaign by associating it with a free(available) website.
     *
     * @param array $data
     * @return void
     */
    public static function make(array $data)
    {
        $website_id = (int) $data['website'];
        $website = Website::find($website_id);
        $data = array_except($data, ['website']);
        $website->campaign()->create($data);
    }

    /**
     * Check if campaign is stopped.
     *
     * @return bool
     */
    public function isStopped()
    {
        return $this->status === 'stopped';
    }

    /**
     * Check if campaign is running.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->status === 'running';
    }

    /**
     * Check if campaign is paused.
     *
     * @return bool
     */
    public function isPaused()
    {
        return $this->status === 'paused';
    }

    /**
     * Check if campaign is of type post.
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->type === 'post';
    }

    /**
     * Check if campaign is of type like.
     *
     * @return bool
     */
    public function isLike()
    {
        return $this->type === 'like';
    }

    /**
     * Check if campaign is of type retweet.
     *
     * @return bool
     */
    public function isRetweet()
    {
        return $this->type === 'retweet';
    }

    /**
     * Change active state.
     *
     * @return void
     */
    public function toggleActive()
    {
        $this->active = ! $this->active;
        $this->save();
    }

    /**
     * Change current status.
     *
     * @param string $status
     * @return void
     */
    public function changeStatusTo($status)
    {
        $this->update(['status' => $status]);
    }

    /**
     * Show a graphical display of the campaing's type.
     *
     * @return string
     */
    public function displayType() 
    {
        if ($this->isPost()) {
            return '<i style="font-size:20px;color:#1DA1F2;" class="fa fa-twitter" aria-hidden="true"></i>';
        } elseif ($this->isLike()) {
            return '<i style="font-size:20px;color:#E2264D;" class="fa fa-heart" aria-hidden="true"></i>';
        } elseif ($this->isRetweet()) {
            return '<i style="font-size:20px;color:#50D9A3;" class="fa fa-retweet" aria-hidden="true"></i>';
        } else {
            throw new \Exception('Unknown campaign type!');
        }
    }

    /**
     * Show a graphical display of the campaing's active state.
     *
     * @return string
     */
    public function displayActive() 
    {
        if ($this->active) {
            return '<i style="font-size:25px;color:#449D44;" class="fa fa-check" aria-hidden="true"></i>';
        } else {
            return '<i style="font-size:25px;color:#C9302C;" class="fa fa-times" aria-hidden="true"></i>';
        }
    }

    /**
     * Show a graphical display of the campaing's action buttons.
     *
     * @return string
     */
    public function displayAction()
    {
        if ($this->isStopped()) {
            return "<button type='button' data-action={$this->id} class='btn btn-success btn-sm start-campaign campaign-action'><i class='fa fa-play' aria-hidden='true'></i> Start</button>
                    <button type='button' data-action={$this->id} class='btn btn-danger btn-sm stop-campaign campaign-action' style='display:none;'><i class='fa fa-stop' aria-hidden='true'></i> Stop</button>
                    <button type='button' data-action={$this->id} class='btn btn-primary btn-sm pause-campaign campaign-action' style='display:none;'><i class='fa fa-pause' aria-hidden='true'></i> Pause</button>
                    <button type='button' data-action={$this->id} class='btn btn-info btn-sm resume-campaign campaign-action' style='display:none;'><i class='fa fa-step-forward' aria-hidden='true'></i> Resume</button>";
        } elseif ($this->isRunning()) {
            return "<button type='button' data-action={$this->id} class='btn btn-success btn-sm start-campaign campaign-action' style='display:none;'><i class='fa fa-play' aria-hidden='true'></i> Start</button>
                    <button type='button' data-action={$this->id} class='btn btn-danger btn-sm stop-campaign campaign-action'><i class='fa fa-stop' aria-hidden='true'></i> Stop</button>
                    <button type='button' data-action={$this->id} class='btn btn-primary btn-sm pause-campaign campaign-action'><i class='fa fa-pause' aria-hidden='true'></i> Pause</button>
                    <button type='button' data-action={$this->id} class='btn btn-info btn-sm resume-campaign campaign-action' style='display:none;'><i class='fa fa-step-forward' aria-hidden='true'></i> Resume</button>";           
        } elseif ($this->isPaused()) {
            return "<button type='button' data-action={$this->id} class='btn btn-success btn-sm start-campaign campaign-action' style='display:none;'><i class='fa fa-play' aria-hidden='true'></i> Start</button>
                    <button type='button' data-action={$this->id} class='btn btn-danger btn-sm stop-campaign campaign-action' style='display:none;'><i class='fa fa-stop' aria-hidden='true'></i> Stop</button>
                    <button type='button' data-action={$this->id} class='btn btn-primary btn-sm pause-campaign campaign-action' style='display:none;'><i class='fa fa-pause' aria-hidden='true'></i> Pause</button>
                    <button type='button' data-action={$this->id} class='btn btn-info btn-sm resume-campaign campaign-action'><i class='fa fa-step-forward' aria-hidden='true'></i> Resume</button>";            
        } else {
            throw new \Exception('Unknown campaign action!');
        }
    }

    /**
     * Show a graphical display of the campaing's current status.
     *
     * @return string
     */
    public function displayStatus() 
    {
        if ($this->isStopped()) {
            return "<i data-status={$this->id} style='font-size:20px;color:#C9302C;' class='fa fa-stop stopped-status-icon campaign-status' aria-hidden='true'></i>
                    <i data-status={$this->id} style='display:none;font-size:20px;color:#449D44' class='fa fa-refresh fa-spin fa-3x fa-fw running-status-icon campaign-status'></i><span class='sr-only'>Loading...</span>
                    <i data-status={$this->id} style='display:none;font-size:20px;color:#2579A9;' class='fa fa-pause paused-status-icon campaign-status' aria-hidden='true'></i>";
        } elseif ($this->isRunning()) {
            return "<i data-status={$this->id} style='display:none;font-size:20px;color:#C9302C;' class='fa fa-stop stopped-status-icon campaign-status' aria-hidden='true'></i>
                    <i data-status={$this->id} style='font-size:20px;color:#449D44' class='fa fa-refresh fa-spin fa-3x fa-fw running-status-icon campaign-status'></i><span class='sr-only'>Loading...</span>
                    <i data-status={$this->id} style='display:none;font-size:20px;color:#2579A9;' class='fa fa-pause paused-status-icon campaign-status' aria-hidden='true'></i>";
        } elseif ($this->isPaused()) {
            return "<i data-status={$this->id} style='display:none;font-size:20px;color:#C9302C;' class='fa fa-stop stopped-status-icon campaign-status' aria-hidden='true'></i>
                    <i data-status={$this->id} style='display:none;font-size:20px;color:#449D44' class='fa fa-refresh fa-spin fa-3x fa-fw running-status-icon campaign-status'></i><span class='sr-only'>Loading...</span>
                    <i data-status={$this->id} style='font-size:20px;color:#2579A9;' class='fa fa-pause paused-status-icon campaign-status' aria-hidden='true'></i>";
        } else {
            throw new \Exception('Unknown campaign status!');
        }
    }

    /**
     * Queue name where the campaign sends publish jobs.
     *
     * @param string $index
     * @return string
     */
    public function getPublishQueue($index)
    {
        return "campaign-{$this->id}-publish-{$index}";
    }

    /**
     * Queue name where the campaign sends delete jobs.
     *
     * @param string $index
     * @return string
     */
    public function getDeleteQueue($index)
    {
        return "campaign-{$this->id}-delete-{$index}";
    }

    /**
     * Start campaign. Go go go:)
     *
     * @return void
     */
    public function start()
    {
        $num_tokens = $this->website->getValidTokensCount();
        $num_workers = static::NUM_WORKERS;
        // Calculate how much load every worker should cary
        $worker_load = ceil($num_tokens/$num_workers);
        // Distribute the tasks uniformly based on worler load
        for ($i=0; $i<$num_workers; $i++) {
            $offset = $i * $worker_load;
            $tokens = $this->website->tokens()->valid()
                                              ->skip($offset)
                                              ->take($worker_load)
                                              ->get();
            // Woker indexes start at 1 an not 0
            $index = $i+1;            
        }
    }

    /**
     * Campaign belongs to a Website.
     *
     * @return BelongsTo
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}

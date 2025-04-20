<?php

namespace App\Providers;

use App\Concretes\ChannelService;
use App\Concretes\NotifyService;
use App\Concretes\NotTypeService;
use App\Interfaces\INotifyService;
use App\Interfaces\INotTypeService;
use App\Interfaces\IChannel;
use App\Interfaces\IChannelConfig;
use App\Interfaces\IChannelService;
use App\Interfaces\IConfigureVar;
use App\Interfaces\IEmail;
use App\Interfaces\IEmailGroup;
use App\Interfaces\IMailingGroupEmail;
use App\Interfaces\INotChannel;
use App\Interfaces\INotType;
use App\Interfaces\INotTypeMailGroup;
use App\Interfaces\IProgNotType;
use App\Interfaces\IProgram;
use App\Interfaces\IVariable;
use App\Interfaces\ITemplate;
use App\Repos\Channel;
use App\Repos\ChannelConfig;
use App\Repos\ConfigVar;
use App\Repos\Email;
use App\Repos\EmailGroup;
use App\Repos\MailingGroupEmail;
use App\Repos\NotChannel;
use App\Repos\NotType;
use App\Repos\NotTypeMailGroup;
use App\Repos\ProgNotType;
use App\Repos\Program;
use App\Repos\Variable;
use App\Repos\Template;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class MyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(INotifyService::class, NotifyService::class);
        $this->app->singleton(INotTypeService::class, NotTypeService::class);
        $this->app->singleton(IChannelService::class, ChannelService::class);

        $this->app->singleton(INotType::class, NotType::class);
        $this->app->singleton(IProgNotType::class, ProgNotType::class);
        $this->app->singleton(IVariable::class, Variable::class);

        $this->app->singleton(IProgram::class, Program::class);
        $this->app->singleton(ITemplate::class, Template::class);
        $this->app->singleton(IMailingGroupEmail::class, MailingGroupEmail::class);
        $this->app->singleton(IChannel::class, Channel::class);
        $this->app->singleton(IChannelConfig::class, ChannelConfig::class);
        $this->app->singleton(INotTypeMailGroup::class, NotTypeMailGroup::class);

        $this->app->singleton(INotChannel::class, NotChannel::class);
        $this->app->singleton(IConfigureVar::class, ConfigVar::class);
        $this->app->singleton(IEmail::class, Email::class);
        $this->app->singleton(IEmailGroup::class, EmailGroup::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

}

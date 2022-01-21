<?php

namespace App\Http\Livewire;

use App\Tools;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Auth;
class ConnectionForm extends Component
{
    public $is_user_alredy_exist = '';
    public $url_redirection = '';
    public Connection $connection;
    //public $user_id;

    public function rules(): array
    {
        return [
            'connection.user_id'=>['required'],
            'connection.name' => ['required', 'string','unique:connections,name'],
            'connection.base_url' => ['required', 'url'],
        ];
    }


    public function getUsersProperty()
    {
        $auth = Auth::user();
        if($auth->role == 'admin'){
            return User::paginate(10);
        } 
    }
    public function mount(Connection $connection): void
    {
        
        $this->connection = $connection;
        if(Auth::user()->role == 'user'){
            $this->connection->user_id = Auth::user()->id;
        }
        
        $this->connection->tool = Tools::current();

        if (! $this->connection->exists) {
            switch ($this->connection->tool) {
                case Tools::REFERER:
                case Tools::SYNDICATION:
                    $this->connection->type = Connection::TYPE_FTP;
                    $this->connection->port = 21;
                    break;
                case Tools::NINJA_FUNNELS:
                    $this->connection->type = Connection::TYPE_FTP;
                    break;
            }
        }
    }

    public function updatedConnectionType(): void
    {
        $this->clearValidation();
    }

    public function types(): array
    {
        return Connection::typesByTool(Tools::current());
    }

    public function submit()
    {
        $url = $this->connection->base_url;
        $parse = parse_url($url);
        $url = substr($url, 0, strrpos( $url, '/'));
        if(!empty($parse)){
            $ch = curl_init($url);
            curl_setopt ( $ch, CURLOPT_URL, $url );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
            $http_respond = curl_exec($ch);
            $http_respond = trim( strip_tags( $http_respond ) );
            $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            if(( $http_code == "200" ) || ( $http_code == "302" ))
            {
                //$this->connection->base_url=$url;
                $this->url_redirection = 'false';
            }
            else
            {
                $this->url_redirection = 'true';
            }
        }
        if(Auth::user()->role == 'admin'){
            if($this->connection->id == ''){
                $this->validate();
            }
            if($this->url_redirection == 'false'){
                $this->connection->save();
                $this->redirectRoute('connections.index');
            }
            
        }
        else{
            $result = Connection::where('user_id', Auth::user()->id)->exists();
        
            if($result == 1 && Auth::user()->role == 'user'){
                $this->is_user_alredy_exist = 'yes';
            }
            else{
                if($this->url_redirection == 'false'){
                    $this->connection->save();
                    $this->redirectRoute('connections.index');
                }
            }

        }
        
    }
}

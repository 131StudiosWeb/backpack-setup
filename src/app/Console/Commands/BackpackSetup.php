<?php

namespace onethirtyone\backpacksetup\App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class PortalInstall
 *
 * @package App\Console\Commands
 */
class BackpackSetup extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onethirtyone:backpack-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Backpack for Laravel setup process.';


    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->validateModels()) {
            return $this->warn('Before proceeding please add the HasRoles and CrudTrait traits to your User Model');
        }

        switch ($this->getChoice()) {
            case 0:
                $this->createRolesAndPermissions();
            case 1:
                $this->createUser();
                break;
        }

        $this->goodbye();
    }

    /**
     * @return string
     */
    public function getChoice()
    {
        return $this->choice('What would you like to do?', [
            'Run Initial Backpack Setup',
            'Create new Administrator',
        ], 0);
    }

    /**
     * @return bool
     */
    public function validateModels()
    {
        foreach ($this->traits() as $trait) {
            if (!in_array($trait, class_uses(new User()))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function traits()
    {
        return [
            'Backpack\CRUD\CrudTrait',
            'Spatie\Permission\Traits\HasRoles',
        ];
    }

    /**
     *  Create Portal Roles
     */
    public function createRolesAndPermissions()
    {
        $this->comment('Creating Roles & Permissions');

        foreach ($this->permissions() as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        foreach ($this->roles() as $role) {
            Artisan::call('cache:clear');

            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role])->givePermissionTo($this->permissions());
            }
        }
    }

    /**
     * @return array
     */
    public function roles()
    {
        return config('onethirtyone.backpacksetup.roles');
    }

    /**
     * @return array
     */
    public function permissions()
    {
        return config('onethirtyone.backpacksetup.permissions');
    }

    /**
     * Creates a new User
     */
    public function createUser()
    {
        $user = User::create($this->getInputs())->assignRole($this->roles());
    }

    /**
     * Get user inputs
     */
    public function getInputs()
    {
        return [
            'name' => $this->ask('Full name of Administrator'),
            'email' => $this->ask('Email address of Administrator'),
            'password' => bcrypt($this->ask('Password for Administrator Account')),
        ];
    }

    /**
     *  Prints setup complete message
     */
    public function goodbye()
    {
        return $this->comment('Setup process complete.');
    }
}

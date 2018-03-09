<?php

namespace onethirtyone\backpacksetup\App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
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
    protected $signature = 'backpack:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the backup user setup';


    /**
     * Create a new command instance.
     *
     */
    public function __construct ()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle ()
    {
        if ($this->validateModels()) {
            $this->createRolesAndPermissions();
            $this->createUser();
        }

        $this->warn('Before proceeding add the HasRoles and CrudTrait traits to your User Model');

    }

    /**
     * @return bool
     */
    public function validateModels ()
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
    public
    function traits ()
    {
        return [
            'Backpack\CRUD\CrudTrait',
            'Spatie\Permission\Traits\HasRoles',
        ];
    }

    /**
     *  Create Portal Roles
     */
    public
    function createRolesAndPermissions ()
    {
        foreach ($this->roles() as $role) {
            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role]);
            }
        }

        foreach ($this->permissions() as $permission) {
            if (!Permission::where(['name', $permission])->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        dd(Permission::all());
    }

    public function roles ()
    {
        return ['administrator'];
    }

    public function permissions ()
    {
        return ['Access Admin Panel'];
    }

    /**
     * Creates a new User
     */
    public
    function createUser ()
    {
        $user = User::create($this->getInputs());
        $user->assignRole($this->roles());
        $user->givePermissionTo($this->permissions());
    }

    /**
     * Get user inputs
     */
    public
    function getInputs ()
    {
        return [
            'name'     => $this->ask('Full name of Administrator'),
            'email'    => $this->ask('Email address of Administrator'),
            'password' => bcrypt($this->ask('Password for Administrator Account')),
        ];
    }
}

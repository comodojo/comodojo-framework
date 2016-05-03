<?php namespace Comodojo\Test\Package;

use \Comodojo\Test\Base;
use \Comodojo\Package\Controller as PackageController;
use \Comodojo\Package\View as PackageView;
use \Comodojo\Package\Iterator as PackageIterator;

class PackageTest extends Base {

    protected $package_name;
    protected $package_version = "1.0.0";

    public function testCreateAndReadPackage() {

        $package_name = "Test".time();
        $package_version = "1.0.0";

        $package = new PackageController($this->configuration(), $this->database());

        $package->package = $package_name;
        $package->version = $package_version;
        $data = $package->persist();

        $id = $data->id;

        $this->assertInstanceOf('Comodojo\Package\Controller', $data);
        $this->assertInternalType('int', $id);

        $packager = new PackageView($this->configuration);
        $packager->load($id);

        $this->assertEquals($package_name, $package->package);
        $this->assertEquals($package_version, $package->version);

    }

    public function testListAndDeletePackages() {

        $packages = PackageIterator::load($this->configuration, $this->database, true);

        foreach ($packages as $package) {

            $this->assertInstanceOf('Comodojo\Package\Controller', $package);

            $result = $package->delete();

            $this->assertTrue($result);

        }

    }

}

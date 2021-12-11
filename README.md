# enumerations

A simple class to handle enumerations

## why?

In some situations we have to handle groups of static values, enumerations comes to deal with these values in a safe
way. Instead of write raw values in your code you can use objects, preventing typo errors and behaviour bugs. It is not
about wraps values into constants, not, you can use TypeHint to avoid wrong calls in methods and constructors.

## Use cases

### Static values: statuses, database enums and options

If you have a currier system you can handle the package states using enumerations: sent, in way, received. Example:

```php 
<?php

final class PakageState extends Jomisacu\Enumerations\Enumeration
{
    public const SENT = 'sent';
    public const IN_WAY = 'in-way';
    public const RECEIVED = 'received';
}

// retrieving from raw values
$state = PackageState::fromValue($valueFromRequest); // throws an exception if invalid value

// TypeHint
class Package
{
    private PackageState $state;
    
    public function changePackageState(PackageState $state)
    {
        $this->state = $state;
    }
    
    public function getPackageState()
    {
        return $this->state;
    }
}

// printing the value
$package = new Package;
$state = PackageState::fromValue($valueFromRequest); // throws an exception if invalid value
$package->changePackageState($state);

echo $package->getPackageState(); // prints sent, in-way or received

```

Now, you can handle these values in a safe way.

### External values

In some situations we need to handle values that can change by external reasons. Imagine an university that offers
multiple college career and by convention they take the decision of apply three code character. Over the time, careers
are added, subjects change, etc., etc. So they decide add a prefix to expand the size of code and maintain the same size
for all. i.e: Software Engineer could have the code 'XYZ' but after change could be '0XYZ'.

The previous change introduces the problems below:

1. Logic broken because the raw values in the code not have mean
2. The values in the database are now corrupted because no match with the real values
3. No safe way to replace the values in a production system, we need to catch errors by the way

The solutions are the enumerations. See the example...

```php 
class Career extends Jomisacu\Enumerations\Enumeration 
{
    // WTF??? What is this?
    // using our own value we drop the dependency with external sources
    // but below we will see how to deal with these values
    // the values in the database are the values that we decided in the class constants
    public const SOFTWARE_ENGINEER = "a372d961-22d9-4cc4-a9ee-4cb47a15b26d";
    public const ARCHITECT = "6d8165dc-621d-4279-bc71-4e4f4782d972";
    
    // we always can get the current code
    // if external code changes we only need to update the code here
    // the values in the database are the values that we decided in the class constants
    public function getCode()
    {
        $codes = [
            self::SOFTWARE_ENGINEER => '0XYZ',
            self::ARCHITECT => '0YYK',
        ];
        
        return $codes[(string) $this->getValue()];
    }
    
    // now, we can store a reference to the previous code, so we can interop with old formats 
    public function getOldCode()
    {
        $codes = [
            self::SOFTWARE_ENGINEER => 'XYZ',
            self::ARCHITECT => 'YYK',
        ];
        
        return $codes[(string) $this->getValue()];
    }
}
```

Enjoy it!

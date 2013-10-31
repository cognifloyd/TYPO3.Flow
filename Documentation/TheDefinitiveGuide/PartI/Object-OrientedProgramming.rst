Object-oriented programming is a programming paradigm applied in TYPO3 Flow and in the packages built upon it. In this section we will give an overview of the
basic concepts of object orientation.

Programs have a certain purpose, which is - generally speaking - to solve a
problem. "Problem" does not necessarily mean error or defect but rather an
actual task. This problem usually has a concrete counterpart in real life.

A program could for example take care of the task of booking a cruise in the
Indian Ocean. Obviously we have such a problem (a programmer that has been
working too much and has finally decided to go on vacation) and a program that promises recuperation by booking a coach on a luxury liner for him and
his wife.

Object orientation assumes that a concrete problem can be solved by a
program, and that the concrete problem can be represented by real objects. Therefore focus is on the object. In reality the object can be abstract: it will not always be something as concrete as a car or a ship, but rather something like a reservation,
an account or a graphical symbol.

Objects are "containers" for data and their corresponding functionality. The data of
an object is stored in its **Properties**. The functionality is provided by
**Methods**, which can, for example, alter the properties of the object.
In regard to the cruise liner we can say that its properties include a certain number of
coaches, a length, a width, and a maximum speed. Furthermore it has methods to
start the motor (and hopefully also to stop it again), change direction, and to increase thrust, so that you can reach your holiday destination a bit faster.

Why Object Orientation at all?
=================================

Surely some users will ask themselves why they should develop object-oriented
in the first place. Why not (just like until now) keep on developing
procedurally, stringing together functions?

If we look at the roughly 4,300 extensions available for TYPO3 CMS at the moment, we'll see that they are built with a class by default - but have been completed by the extension developer in a procedural way in about 95% of all cases.

Procedural programming has some severe disadvantages though:

- Properties and methods belonging together with regard to content can not be organized in a group fashion. This methodology, called **Encapsulation** in object orientation, is necessary  if only in order to manage organization of code.
- It is rather difficult to re-use code
- All properties can be altered everywhere throughout the code. This leads to hard-to-find errors.
- Procedural code can get confusing quite quickly. This is called Spaghetti code.

Object orientation represents objects in the real world: real objects exist, and they all have properties and (most of them) methods. These relations are now represented in programming.

In the following we'll talk about the ship object. We'll invoke this object,
stock it with coaches, a motor and other useful stuff. Furthermore, there will
be functions like moving the ship or turning the motor on and off. Later we'll
even create a luxury liner based on the general ship and equip it with a golf
simulator and satellite TV.

On the following pages, we'll try to be as literal as possible (while still remaining 
semantically correct) in order to familiarize you with object orientation. There is a
specific reason for this: the more you can identify with the object and its methods, the
more you'll be able to understand the theory behind object oriented programming.
Both are necessary in order to program successfully – even though you'll not always be able to imagine the objects that you'll work with later as clearly as in our examples.

Classes and Objects
===================

Let's now take a step back and imagine that there is a blueprint for ships
in general. We will now focus not on a ship but on this blueprint. It is called
**class**, in this case it is the class ``Ship``. In PHP this is written as
follows;

PHP Code::

	<?php

	class Ship {

	...

	}

	?>

.. note::
	In this piece of code we wrote the necessary PHP tags at the
	beginning and end. We will spare them in the following examples to make the
	examples a bit shorter.

The key word ``class`` opens the class and inside the curly brackets we could have properties and methods. We will now add these properties and methods:

PHP Code::

	class Ship {

		public $name;
		public $coaches;
		public $engineStatus;
		public $speed;


		function startEngine() {}
		function stopEngine() {}
		function moveTo($location) {}

	}

Our ship now has a name (``$name``\ ), a number of coaches (``$coaches``\ ) and a
speed (``$speed``\ ). In addition we built in a variable, containing the status
of the engine (``$engineStatus``\ ). A real ship, of course, has much more
properties, all important in some way, but for our abstraction these few will be
sufficient. We'll explain why every property is marked with the key
word ``public`` further down.

.. note::
	For methods and properties we use a notation called **lowerCamelCase**: The first letter is lower case and all other parts are added without blank or
underscore in upper case. This is a convention used in TYPO3 Flow.

We can also switch on the engine (``startEngine()``\ ), travel with the ship to
the desired destination (``moveTo($location)``) and switch off the engine
again (``stopEngine()``\ ). Note that all methods are empty, i.e. we have no
content at all. We'll change this in the following examples, of course. The
line containing method name and (if available) parameters is called the method
signature or method head. Everything contained by the method is referred to as the  method body.

Now we'll finally create an object from our class. The class ``ship`` will be
the blueprint and ``$fidelio`` the concrete object.

PHP Code::

	$fidelio = new Ship();

	// Display the object
	var_dump($fidelio);

The key word "new" is used to create a concrete object from the (blueprint) class.
This object is also called an **Instance **and the creation process
consequentially, **Instantiation**. We can use the command ``var_dump()`` to more
closely examine the object. We'll see the following

PHP Code::

	object(Ship)#1 (3) {

		["name"] => NULL

		["coaches"] => NULL

		["engineStatus"] => NULL

		["speed"] => NULL

	}

We can clearly see that our object has 4 properties with concrete values - at
the moment still NULL, for we have not yet assigned anything. We can instantiate
as many objects from a class as we like, and every single one will differ from
the others – even if all of the properties have the same values.

PHP Code::

	$fidelio1 = new Ship();
	$fidelio2 = new Ship();

	if ($fidelio1 === $fidelio2) {
		echo 'objects are identical!'
	} else {
		echo 'objects are not identical!'
	}

In this example the output is ``objects are not identical!``

The arrow operator
------------------

We are able to create an object now, but of course it's properties are
still empty. We'll hurry to change this by assigning values to the properties.
For this, we use a special operator, the so-called arrow operator (->). We can
use it for getting access to the properties of an object or calling methods. In
the following example, we set the name of the ship and call some methods:

PHP Code::

	$ship = new Ship();
	$ship->name = "FIDELIO";

	echo "The ship's Name is ". $ship->name;

	$ship->startEngine();
	$ship->moveTo('Bahamas');
	$ship->stopEngine();


$this
-----

Using the arrow operator, we can now comfortably access properties and methods
of an object. But what do we do if we want to do this from inside a method, e.g.
to set ``$speed ``inside of the method ``startEngine()``? We don't know at this
point how an object that will later be instantiated will be called. So we need a
mechanism to do this regardless of the name. This is done with the special
variable ``$this``.

PHP Code::

	class Ship {

		...

		public $speed;

		...

		function startEngine() {

			$this->speed = 200;

		}

	}

With ``$this->speed`` you can access the property *speed* within the actual object,
independently of its name.

Constructor
-----------

It can be very useful to initialize an objects properties at the moment of its
instantiation. Surely there will be a certain number of coaches built in
right away, when a new cruise liner is created - so that the future guest will
not be forced to sleep in emergency accommodation. So we should define the number of coaches at the time an object is instantiated. This assignment of a given value is done within a method that is automatically called on creation of an object.  This method is the so-called **Constructor**, which always has the name ``__construct()`` (the first two characters are underscores).

The values received at instantiation are passed to the constructor as
arguments and then assigned to the properties ``$coaches `` and ``$name``, respectively.


Inheritance of Classes
======================

With the class we created we can already do a lot. We can create many ships and
send them out to the oceans of the world. But of course the shipping company is always working on improving what their cruise liners have to offer. Increasingly big and beautiful ships are built. New offers for the passengers are also added. FIDELIO2, for example, even has a little golf course based on deck.

If we look behind the curtain of this new luxury liner though, we find that the
shipping company only took a ship type FIDELIO and altered it a bit. The basis
is the same. Therefore it makes no sense to completely redefine the new ship –
instead we should use the old definition and just add the golf course – just as the
shipping company did. Technically speaking we extend an "old" class definition
by using the key word ``extends``\.

PHP Code::

	class LuxuryLiner extends Ship {

		public $luxuryCoaches;

		function golfSimulatorStart() {

			echo 'Golf simulator on ship ' . $this->name . '
			started.';

		}

		function golfSimulatorStop() {

			echo 'Golf simulator on ship ' . $this->name . '
			stopped.';

		}

	}

	$luxuryShip = new LuxuryLiner('FIDELIO2','600')

Our new luxury liner comes into existence as easy as that. We define that the
luxury liner just extends the Definition of the class ``Ship``. The extended
class (in or example ``Ship``) is called **parent class **or **superclass**.
The class formed by extension (in our example ``LuxuryLiner``) is called
**child class **or **sub class**.

The class ``LuxuryLiner`` now contains the complete configuration of the base
class ``Ship`` (including all properties and methods) and defines additional
properties (like the amount of luxury coaches in ``$luxuryCoaches``) and
additional methods (like ``golfSimulatorStart()`` and ``golfSimulatorStop()``).
Inside these methods you can again access the properties and methods of the
parent class by using ``$this``.

Overriding Properties and Methods
---------------------------------

Inside an inherited class you can not only access properties and methods of the
parent class or define new ones. It's even possible to override the original
properties and methods. This can be very useful, e.g. for giving a method of
a child class a new functionality. Let's have a look at the method
``startEngine()`` for example:

PHP Code::

	class Ship {
	   ...
	   $engineStatus = 'OFF';
	   ...
	   function startEngine() {
		  $this->engineStatus = 'ON';
	   }
	   ...
	}

	class Luxusliner extends Ship {
	   ...
	   $additionalEngineStatus = 'OFF';
	   ...
	   function startEngine() {
		  $this->engineStatus = 'ON';
		  $this->additionalEngineStatus = 'ON';
	   }
	   ...
	}

Our luxury liner (of course) has an additional motor, so this has to be
switched on as well when the method ``startEngine()`` is called. The child class
now overrides the method of the parent class and so only the method
``startEngine()`` of the child class is called.

Access to the parent class through "parent"
-------------------------------------------

Overriding a method comes in handy, but has a serious disadvantage. When
changing the method ``startEngine()`` in the parent class, we'd also have to
change the method in the child class. This is not only a source of errors but
also kind of inconvenient. It would be better to just call the method of the
parent class and then add additional code before or after the call. That's
exactly what can be done by using the key word ``parent``. With
``parent::methodname()`` the method of the parent class can be accessed
comfortably - so our former example can be re-written in a smarter way:

PHP Code::

	class Ship {
	   ...
	   $engineStatus = 'OFF';
	   ...
	   function startEngine() {
		  $this->engineStatus = 'ON';
	   }
	   ...
	}

	class Luxusliner extends Ship {
	   ...
	   $additionalEngineStatus = 'OFF';
	   ...
	   function startEngine() {
		  parent::startEngine();
		  $this->additionalEngineStatus = 'ON';
	   }
	   ...
	}

Abstract classes
----------------

Sometimes it is useful to define "placeholder methods" in the parent class
which are defined in the child class. These "placeholders" are called
**abstract methods**. A class containing abstract methods is called an **abstract
class**. For our ship there could be a method ``setupCoaches()``. Each type of
ship is to be handled differently because each has its own configuration. So each
ship must have such a method but the concrete implementation is to be done
separately for each ship type.

PHP Code::

	abstract class Ship {
	...
	   function __construct() {
		  $this->setupCoaches();
	   }
	   abstract function setupCoaches();
	...
	}

	class Luxusliner extends Ship {
	...
	   function setupCoaches() {
		  echo 'Coaches are being set up';
	   }
	}

	$luxusschiff = new Luxusliner();

In the parent class we have defined only the body of the
method ``setupCoaches()``. The key word ``abstract`` makes sure that the method
must be implemented in the child class. So, using abstract classes, we can
define which methods have to be present later without having to implement them
right away.

Interfaces
----------

Interfaces are a special case of abstract classes in which **all methods** are
abstract. Using Interfaces, specification and implementation of functionality
can be kept apart. In our cruise example we have some ships supporting
satellite TV and some who don't. The ships who do, have the methods
``enableTV()`` and ``disableTV()``. It is useful to define an interface
for that:

PHP Code::

	interface SatelliteTV {
	   public function enableTV();
	   public function disableTV();
	}

	class Luxusliner extends Ship implements SatelliteTV {

	   protected $tvEnabled = FALSE;

	   public function enableTV() {
		  $this->tvEnabled = TRUE;
	   }
	   public function disableTV() {
		  $this->tvEnabled = FALSE;
	   }
	}

Using the key word ``implements`` ensures that the class implements
the given interface. All methods in the interface definition must be created in the class. The object ``LuxuryLiner`` now is of the type ``Ship`` but also of
the type ``SatelliteTV``. It is also possible to implement not only one
interface class but multiple, separated by comma. Of course interfaces can also
be inherited by other interfaces.

Visibilities: public, private and protected
===========================================

Access to properties and methods can be restricted by different visibilities to
hide the implementation details of a class. The meaning of a class can be
communicated better like this, since implementation details in internal methods
can not be accessed from outside. The following visibilities exist:

- **public**: properties and methods with this visibility can be accessed
  from outside the object. If no Visibility is defined, the behavior of
  ``public`` is used.
- **protected**: properties and methods with visibility ``protected`` can
  only be accessed from inside the class and its child classes.
- **private**: properties and methods set to ``private`` can only be
  accessed from inside the class itself, not from child classes.

Access to Properties
--------------------

This small example demonstrates how to work with protected properties:

PHP Code::

	abstract class Ship {
	   protected $coaches;
	   ...
	   abstract protected function setupCoaches();
	}

	class Luxusliner extends Ship {
	   protected function setupCoaches() {
		  $this->coaches = 300;
	   }
	}

	$luxusliner = new Luxusliner('Fidelio', 100);
	echo 'Number of coaches: ' . $luxusliner->coaches; // Does NOT work!

The ``LuxuryLiner`` may alter the property ``coaches``, for this is ``protected``.
If it were ``private`` no access from inside of the child class would
be possible. Access from outside of the hierarchy of inheritance (like in the
last line of the example) is not possible. It would only be possible if the
property was ``public``.

We recommend defining all properties as ``protected``. Like that, they can not
be altered any more from outside and you should use special methods (called
getter and setter) to alter or read them. We'll explain the use of these
methods in the following section.

Access to Methods
-----------------

All methods the object makes available to the outside have to be defined as
``public``. All methods containing implementation details, e.g.
``setupCoaches()`` in the above example, should be defined as ``protected``.
The visibility ``private`` should be used most rarely, for it prevents methods
from being overwritten or extended.

Often you'll have to read or set properties of an object from outside. So you'll
need special methods that are able to set or get a property. These methods are
called **setter** and **getter**, respectively. See the example.

PHP Code::

	class Ship {

	   protected $coaches;
	   protected $classification = 'NORMAL';

	   public function getCoaches() {
		  return $this->coaches;
	   }

	   public function setCoaches($numberOfCoaches) {
		  if ($numberOfCoaches > 500) {
			 $this->classification = 'LARGE';
		  } else {
			 $this->classification = 'NORMAL';
		  }
		  $this->coaches = $numberOfCoaches;
	   }

	   public function getClassification() {
		  return $this->classification;
	   }

	   ...
	}

We now have a method ``setCoaches()`` which sets the number of coaches.
Furthermore it changes - depending on the number of coaches - the ship
category. You now see the advantage: When using methods to get and set properties, you can perform more complex operations, for example setting dependent properties. This preserves consistency of the object. If you set
``$coaches`` and ``$classification`` to ``public``, we could set the number of
cabins to 1000 and classification to ``NORMAL`` - and our ship would end up
being inconsistent.

.. note::
	In TYPO3 Flow you'll find getter and setter methods all over. No property in
	TYPO3 Flow is set to ``public``.


Static Methods and Properties
=============================

Until now we've worked with objects, instantiated from classes. Sometimes though,
it does not make sense to generate a complete object just to be able to use a
function of a class. For this, PHP offers the possibility to directly access
properties and methods. These are referred to as ``static properties`` and ``static methods``, respectively. Take this as a rule of thumb: static properties are
necessary every time two instances of a class are to have a common property.
Static methods are often used for function libraries.

Transferred to our example: let's imagine that all ships are constructed by the
same shipyard. In case of technical emergency, all ships need to know the
actual emergency phone number of this shipyard. So we save this number in a
static property ``$shipyardSupportTelephoneNumber``:

PHP Code::

	class Luxusliner extends Ship {
	   protected static $shipyardSupportTelephoneNumber = '+49 30 123456';

	   public function reportTechnicalProblem() {
		  echo 'On the ship ' . $this->name . ' a problem has been discovered.
		        Please inform ' . self::$shipyardSupportTelephoneNumber;
	   }

	   public static function setShipyardSupportTelephoneNumber($newNumber) {
		  self::$shipyardSupportTelephoneNumber = $newNumber;
	   }
	}

	$fidelio = new Luxusliner('Fidelio', 100);
	$figaro = new Luxusliner('Figaro', 200);

	$fidelio->reportTechnicalProblem();
	$figaro->reportTechnicalProblem();

	Luxusliner::setShipyardSupportTelephoneNumber('+01 1000');

	$fidelio->reportTechnicalProblem();
	$figaro->reportTechnicalProblem();

	// Output
	On the ship Fidelio a problem has been discovered. Please inform +49 30 123456
	On the ship Figaro a problem has been discovered. Please inform +49 30 123456
	On the ship Fidelio a problem has been discovered. Please inform +01 1000
	On the ship Figaro a problem has been discovered. Please inform +01 1000

What happens here? We instantiate two different ships, which both have a problem
and do contact the shipyard. Inside the method ``reportTechnicalProblem()`` you
see that if you want to use static properties, you have to trigger them with the
key word ``self::``. If the emergency phone number now changes, the shipyard has
to tell all the ships about the new number. For this it uses the
**static method** ``setShipyardSupportTelephoneNumber($newNumber)``. Since the
method is static, it is called as ``classname::methodname()``,
in our case ``LuxuryLiner::setShipyardSupportTelephoneNumber(...)``.
If you check the latter two problem reports, you see that all instances of the
class use the new phone number. Both ship objects have access to the same
static variable ``$shipyardSupportTelephoneNumber``.

Important design- and architectural patterns
============================================
In software engineering you'll sooner or later stumble upon design problems that are innate and therefore solvable in a repeatable way. Clever people thought up design patterns as general solutions to commonly recurring problems. Each design pattern is, so to speak, a solution template for a specific problem. We have by now multiple design patterns that have been successfully proved in practice and therefore have found their way in to modern programming and especially in to TYPO3 Flow. In the following examples, we don't want to focus on concrete implementation of the design patterns since this knowledge isn't necessary for using TYPO3 Flow. Nevertheless, deeper knowledge of design patterns in general is indispensable for modern programming, so it might be fruitful for you to learn about them.

.. tip::
	Further information about design patterns can be found on
	http://sourcemaking.com/ or in the book **PHP Design Patterns** by Stephan
	Schmidt, published by O'Reilly.

From the large number of design patterns, we will have a closer look at two that are essential when programming with TYPO3 Flow: Singleton and Prototype.

Singleton
---------

This design pattern makes sure that only one instance of a class  can exist
**at a time**. In TYPO3 Flow you can mark a class as singleton by annotating it
with ``@Flow\Scope("singleton")``. As an example: our luxury liners are all constructed in the same shipyard, so there is no sense in having more than one instance of the shipyard object:

PHP Code::

	/**
	 * @Flow\Scope("singleton")
	 */
	class LuxuslinerShipyard {
	   protected $numberOfShipsBuilt = 0;

	   public function getNumberOfShipsBuilt() {
		  return $this->numberOfShipsBuilt;
	   }

	   public function buildShip() {
		  $this->numberOfShipsBuilt++;
		  // Schiff bauen und zurückgeben
	   }
	}

	$luxuslinerShipyard = new LuxuslinerShipyard();
	$luxuslinerShipyard->buildShip();

	$theSameLuxuslinerShipyard = new LuxuslinerShipyard();
	$theSameLuxuslinerShipyard->buildShip();

	echo $luxuslinerShipyard->getNumberOfShipsBuilt(); // 2
	echo $theSameLuxuslinerShipyard->getNumberOfShipsBuilt(); // 2

Prototype
---------

Prototype is sort of the complement to Singleton. While for each class only one
object is instantiated when using Singleton, it is explicitly allowed to have
multiple instances when using Prototype. Each class annotated with
``@Flow\Scope("prototype")`` is of type **Prototype**. Since this is the default
scope, you can safely leave this one out.

.. note::
	Originally for the design pattern **Prototype**, it is specified that a new
	object is to be created by cloning an object prototype. We use Prototype as
	counterpart to Singleton, however, without a concrete pattern implementation   	in the background. For the functionality we experience, this does not make
	any difference: We invariably get back a new instance of a class.

Now that we've refreshed your knowledge of object oriented programming, we can
take a look at the deeper concepts of TYPO3 Flow: Domain Driven Design,
Model View Controller and Test Driven Development. You'll spot the basics we
just talked about in the following sections frequently.


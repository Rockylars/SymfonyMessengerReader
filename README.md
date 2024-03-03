### Introduction
For some reason, [Symfony Messenger](https://github.com/symfony/messenger) lacks a command to read from a Transport, or even simpler, from a Receiver.
This becomes a problem when using a Messenger solution that implements a non readable serializing solution (so instead of JSON, you use the default PHP Serializer) or a database with soft deletes, or delayed deletes.

There is a Transport debug command (`debug:messenger`) but no way of quickly and temporarily deserializing the Message to understand what's inside of it.
This "reading" of a Message is useful as it won't send it to the actual transport but just prints out the data inside.
Just reading what's inside of it will show you immediately why it would be failing, especially if you chose to not use the Failure bus, as you then can't use the "failed messages show" command (`messenger:fail:show`).

This command adds an easy and universal way of printing out the whole object in either compressed, easily copyable JSON (as Message objects can get quite large with the class properties) or human readable JSON.
We use the [JMS Serializer](https://github.com/schmittjoh/serializer) to accomplish this, there is also a [bundle version](https://github.com/schmittjoh/JMSSerializerBundle) but since this is such a small project, unless someone would be able to help me out with setting it up, I don't think it's needed.
Not using the bundle while someone is using this package will mean we're ignoring their config, though since this is a one location one goal thing, again I don't think that's the end of the world.

### History
This was developed around March of 2023 while working at [Future500](https://future500.nl/) on a project for my former company, [FinanceMatters](https://www.financematters.nl/)/[BondCenter](https://www.bondcenter.nl/).
Just like my project "[Faker](https://github.com/Rockylars/Faker)", there was sadly barely any interest in the code needed for this reading, though they were also surprised such a command doesn't exist on the base Symfony level.
The lucky part was that in similar ways, they allowed me to republish the code once extracted.

### Usage
I recommend making your own command alongside of this.
This reader will be generic and thus won't have the extra bells and whistles that your application might have.
This reader should work for every situation, but it has problems with classes that got altered while the messages are still serialized in the old format, a problem which I never really figured out how to solve in a generic way that would somehow know the deserialization and how to convert it to a simple (object) type.

I will leave an example project in that is needed to test if the bundle itself works correctly, AKA if it'll bind automatically.
Inside of this example project I shall leave a custom command that I also made at that time, though detached from the original project it belonged to.

### Examples
This is just a command call, so there won't be an example.
There will be the mentioned "custom reader" example, but you gotta find it yourself in my project code.
It won't be useful to you aside of showing how you can make your own, if you have an interest in that, surely you will also have the capacity to find it in the folders.
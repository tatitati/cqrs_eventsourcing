# 1. Setup
Install/run rabbitmq (our system publish async projections into rabbitmq)
```
brew install rabbitmq
brew services start rabbitmq
```

Install/run mysql
```
brew install mysql
brew services start mysql
```

Set code
```
composer install
bin/console doctrine:database:create
bin/console doctrine:schema:create
bin/phpunit
```




# 2. Now you can play, for example:
```
php bin/console account:create --email='whatever' --amount=10
php bin/console account:update --email='whatever' --amount=25
php bin/console account:update --email='whatever' --amount=-7
php bin/console account:update --email='whatever' --amount=-1000
```

Create snapshot (this should be automated with a cron job)

```php bin/console account:snapshot --email='whatever'```

Create some more events if you wish after an snapshot

```php bin/console account:update --email='whatever' --amount=1000```

Get final state of an account (reconstitute)

```php bin/console account:reconstitute --email='whatever'```



## Event store example:

<table>
    <tr>
        <th>id</th>
        <th>source_id</th>
        <th>event_type</th>
        <th>occurred_on</th>
        <th>body_event</th>
    </tr>
    <tr>
        <td>71</td>
        <td>whatever</td>
        <td>AccountWasCreated</td>
        <td>2018-10-15 21:51:26</td>
        <td>&#123;&#34;eventId&#34;:&#34;fb26ec69-345b-4664-ad0b-2bea7cb6ee85&#34;&#44;&#34;amount&#34;:10&#44;&#34;sourceId&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-15T21:51:26.388185&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasCreated&#34;&#125;</td>
    </tr>
    <tr>
        <td>72</td>
        <td>whatever</td>
        <td>AccountWasDeposited</td>
        <td>2018-10-15 21:51:36</td>
        <td>&#123;&#34;eventId&#34;:&#34;b5ea3b4c-4e7c-4c2b-9663-66683020f8c1&#34;&#44;&#34;amount&#34;:25&#44;&#34;email&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-15T21:51:36.055022&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasDeposited&#34;&#125;</td>
    </tr>
    <tr>
        <td>73</td>
        <td>whatever</td>
        <td>AccountWasWithdrawed</td>
        <td>2018-10-15 21:51:41</td>
        <td>&#123;&#34;eventId&#34;:&#34;8b73a890-f0fa-42ed-b19e-0df1d53e1391&#34;&#44;&#34;amount&#34;:7&#44;&#34;email&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-15T21:51:41.478650&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasWithdrawed&#34;&#125;</td>
    </tr>
    <tr>
        <td>74</td>
        <td>whatever</td>
        <td>AccountWasDeposited</td>
        <td>2018-10-15 21:51:51</td>
        <td>&#123;&#34;eventId&#34;:&#34;121f9dfe-d67f-46c3-9614-9722176927a7&#34;&#44;&#34;amount&#34;:1000&#44;&#34;email&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-15T21:51:51.299146&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasDeposited&#34;&#125;</td>
    </tr>
</table>


## Snapshot store example (snapshot created after the event with id 73):
<table>
    <tr>
        <th>id</th>
        <th>source_id</th>
        <th>occurred_on</th>
        <th>body_snapshot</th>
    </tr>
    <tr>
        <td>43</td>
        <td>whatever</td>
        <td>2018-10-15 21:51:46</td>
        <td>&#123;&#34;email&#34;:&#34;whatever&#34;&#44;&#34;amount&#34;:28&#44;&#34;created_on&#34;:&#34;2018-10-15T21:51:46.089432&#43;02:00&#34;&#125;</td>
    </tr>
</table>



-----
# TODO
## In progress:
- [ ] Investigate versioning for snapshots
- [ ] Add event handler to achieve eventual consistency 
- [ ] Create test database

## INVESTIGATE: Transactional model:

- [x] Add command bus Presenttation -> Application services
- [x] Create domain model with events
- [x] Create Event store
- [ ] Improve seralization of events -> use symfony component
- [ ] Serialization should be moved from domain model?, is an infrastructure concern
- [x] Create Snapshots store
- [x] Create Projector to trigger Async projections to read model
- [x] Create repository for events
- [x] Improve concepts of DTOs between layers and interfaces
- [x] Complete DI, autowire, etc.
- [x] Finish CLI-commands and rename folder to "presentation" layer and use command bus
- [x] Create publishers for projections in RabbitMQ
- [ ] Add specifications pattern for policies (fluent specification?): Useful to validate emails?, is specification used instead for domain more than for application?
- [ ] Add asynchronous transactional commands handling?. Investigate Event bus (SimpleBus can handle commands, events and async!!!)
- [x] IMPORTANT! ___ Investigate how to create unique identifiers. In domain (GUID), or delegate to infrastructure (Id)?
- [ ] Snapshot date of creation is not testable. Delegate creation date to application service
- [ ] Replace rabbitmq for kafka? (fun?)
- [x] Easiser way to debug doctrine repositories to avoid to make a "data driven" development?
- [ ] Investigate surrogate id. Should be in domain model the surrogate id?, is really necessary?
- [ ] IMPORTANT: domain model identity should be a value objects. Change to VO 
- [x] Investigate how to store a VO into doctrine DB (custom mapping types)


## INVESTIGATE:  Reporting model

- [x] Create DB Doctrine
- [ ] Implement individual projections to read model with Listener-Observer
- [x] Create RabbitMQ consumers to consume write model events

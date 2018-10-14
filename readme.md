
# TODO:
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

----

## 1. Setup

```
composer install
bin/console doctrine:database:creat
bin/console doctrine:schema:create
bin/phpunit
```


## 2. Our system publish async projections into rabbitmq, so you need to start it:
```brew services start rabbitmq```


## 3. Now you can play, for example:
```
php bin/console account:create --email='whatever' --amount=10
php bin/console account:update --email='whatever' --amount=5
```

Create snapshot (this should be automated with a cron job)

```php bin/console account:snapshot --email='whatever'```

Create some more events if you wish after an snapshot

```php bin/console account:update --email='whatever' --amount=2```

Get final state of an account (reconstitute)

```php bin/console account:history --email='whatever'```



# Event store example:

<table>
    <tr>
        <th>id</th>
        <th>source_id</th>
        <th>event_type</th>
        <th>occurred_on</th>
        <th>body_event</th>
    </tr>
    <tr>
        <td>85</td>
        <td>whatever</td>
        <td>AccountWasCreated</td>
        <td>2018-10-14 22:22:23</td>
        <td>&#123;&#34;eventId&#34;:&#34;934c4363-a0e8-449b-a3cf-ea3f89b54d64&#34;&#44;&#34;amount&#34;:10&#44;&#34;sourceId&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-14T22:22:23.971994&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasCreated&#34;&#125;</td>
    </tr>
    <tr>
        <td>86</td>
        <td>whatever</td>
        <td>AccountWasUpdated</td>
        <td>2018-10-14 22:22:30</td>
        <td>&#123;&#34;eventId&#34;:&#34;7eda0635-1f91-4a84-aeee-f1d8b35529f3&#34;&#44;&#34;amount&#34;:5&#44;&#34;email&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-14T22:22:30.714798&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasUpdated&#34;&#125;</td>
    </tr>
    <tr>
        <td>87</td>
        <td>whatever</td>
        <td>AccountWasUpdated</td>
        <td>2018-10-14 22:22:36</td>
        <td>&#123;&#34;eventId&#34;:&#34;5746c492-f828-4ed2-b597-b411f36ce5a4&#34;&#44;&#34;amount&#34;:2&#44;&#34;email&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-14T22:22:36.108211&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasUpdated&#34;&#125;</td>
    </tr>
</table>


# Snapshot store example:
<table>
    <tr>
        <th>id</th>
        <th>source_id</th>
        <th>occurred_on</th>
        <th>body_snapshot</th>
    </tr>
    <tr>
        <td>21</td>
        <td>whatever</td>
        <td>2018-10-14 22:22:38</td>
        <td>&#123;&#34;email&#34;:&#34;whatever&#34;&#44;&#34;amount&#34;:17&#44;&#34;created_on&#34;:&#34;2018-10-14T22:22:38.305103&#43;02:00&#34;&#125;</td>
    </tr>
</table>

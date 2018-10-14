

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
php bin/console account:create --email='whatever' --amount=1
php bin/console account:update --email='whatever' --amount=1010
```

Create snapshot (this should be automated with a cron job)

```php bin/console account:snapshot --email='whatever'```

Create some more events if you wish after an snapshot

```php bin/console account:update --email='whatever' --amount=6666```

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
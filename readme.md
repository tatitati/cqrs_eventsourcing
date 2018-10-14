

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
        <td>82</td>
        <td>whatever</td>
        <td>AccountWasCreated</td>
        <td>2018-10-14 22:18:33</td>
        <td>&#123;&#34;eventId&#34;:&#34;a61813cf-ef3a-4f4e-bc8c-71d222323469&#34;&#44;&#34;amount&#34;:10&#44;&#34;sourceId&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-14T22:18:33.239700&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasCreated&#34;&#125;</td>
    </tr>
    <tr>
        <td>83</td>
        <td>whatever</td>
        <td>AccountWasUpdated</td>
        <td>2018-10-14 22:18:35</td>
        <td>&#123;&#34;eventId&#34;:&#34;2fa0d331-35f2-4404-93e4-be543193297e&#34;&#44;&#34;amount&#34;:1323&#44;&#34;email&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-14T22:18:35.576895&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasUpdated&#34;&#125;</td>
    </tr>
    <tr>
        <td>84</td>
        <td>whatever</td>
        <td>AccountWasUpdated</td>
        <td>2018-10-14 22:18:36</td>
        <td>&#123;&#34;eventId&#34;:&#34;717e9396-852d-44e7-bcd6-cd0f799a89cf&#34;&#44;&#34;amount&#34;:1323&#44;&#34;email&#34;:&#34;whatever&#34;&#44;&#34;createdAt&#34;:&#34;2018-10-14T22:18:36.646475&#43;02:00&#34;&#44;&#34;classSource&#34;:&#34;AccountWasUpdated&#34;&#125;</td>
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
        <td>20</td>
        <td>snapshot&#64;email.com</td>
        <td>2018-10-14 22:16:06</td>
        <td>&#123;&#34;email&#34;:&#34;snapshot&#64;email.com&#34;&#44;&#34;amount&#34;:110&#44;&#34;created_on&#34;:&#34;2018-10-14T22:16:06.559350&#43;02:00&#34;&#125;</td>
    </tr>
</table>
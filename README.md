# fedapi-record-viewer

Quick PHP hack to view FedAPI (http://fedapi.io) records and expose the event history for the specified record.

Consumes the record API with the `event_history` set to `true`.

Used mainly to show business analysts / users the records without having to have them consume the APIs.

## GET parameters that must be passed in URL:

`c` the catalog name

`r` the resource

`t` the type 

`id` the record id

## Example

http://jsonview.540.io/fedapi/?c=fpds&r=fpds&t=award&id=c381664c8a87c76159681237346e03da4760c2a9





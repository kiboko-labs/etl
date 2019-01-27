Kiboko ETL Documentation
========================

E... T... what?
---------------

An ETL is a design pattern aimed at synchronization routines on large scale volumes of data. The 3 letters stand for Extract-Transform-Load.

This library implements this concept in PHP with the help of `Iterator` and `Generator` objects.

Terminology
-----------

* *Data Source*: a data storage on which the pipeline will read the data the processing is about
* *Data Sink*: a data storage on which the pipeline will write the data that has been processed

* *[Pipeline](pipeline.md)*: a suite of steps executed sequentially
* *Pipeline Step*: an unitary operation executed by a pipeline
* *[Extract](extractors.md)*: the pipeline step in charge of reading the raw data source
* *[Transform](transformers.md)*: the pipeline step in charge of transformation and validation operations of the data. It can perform lookup operations in a second-level data source
* *Load*: the pipeline step in charge of the data persistence in the data sink

* *Lookup*: a transformation step doing some data lookup into a secondary data source
* *Validate*: a transformation step doing some data format and integrity checks




#Multi Tenancy

##Flow Config

### Introduction

Flow Config is a key value configuration platform built on top of doctrine.
 It provides an PHP API for setting configuration at the platform that can be set by an install, and then set for a
 user, provider, or other entity. Defaults are set in a single location, rather than scattering them through the code.
 
 Multitenancy depends on the FlowConfig package.

### Installation

1. Register `LoyaltyCorp\FlowConfig\Bridge\Laravel\Providers\FlowConfigServiceProvider` in your `bootstrap/app.php`

# Releasing a new version

This is a guide on how to release a new version of this project. Remember that
when considering the version number to use, that this project follows
[Semantic Versioning](http://semver.org/), so bump the version number accordingly.

Also, note that, for the logs, we follow the [Keep a CHANGELOG](https://keepachangelog.com)
format (sections order, lists, ...) as much as possible.

Prior to tagging a release, ensure the following have been updated:

* The `CHANGELOG.md` needs to be up-to-date.  In addition, the _Unreleased_ section
  needs to be updated with the version being released.  Also update the _Unreleased_
  link at the bottom with the new version number and add the new link there.

When all the changes above have been performed and triple-checked,
**create a commit** (*"Prepare for vX.Y.Z release"*) and
**push it straight upstream** to `main`.

Once all code and commits are in place and verified, you need to tag a
release. Tag `main` branch `HEAD` and push using commands (don't forget the
leading "`v`"):

```bash
$ git tag -a vX.Y.Z -m "Release version vX.Y.Z"
$ git push origin vX.Y.Z
```

When the tag is pushed, GitHub release workflow will be triggered. Verify it has worked
correctly by navigating at [Releases](https://github.com/moodlehq/moodle-cs/releases).

While in that page, optionally, you can edit the release and add any content
to the description, though **it's not recommended** because that may lead to
double release notifications here and there.
